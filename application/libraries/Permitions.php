<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permitions {

    private static $shop_controllers_path = 'application/modules/shop/admin/';
    private static $base_controllers_path = 'application/modules/admin/';
    private static $modules_controllers_path = 'application/modules/';
    private static $rbac_roles_table = 'shop_rbac_roles';
    private static $rbac_privileges_table = 'shop_rbac_privileges';
    private static $rbac_group_table = 'shop_rbac_group';
    private static $rbac_roles_privileges_table = 'shop_rbac_roles_privileges';
    private static $controller_types = array('shop', 'base', 'module');

    public function __construct() {
        $ci = & get_instance();
        $ci->load->library('DX_Auth');
    }

    public static function checkPermitions() {
//        self::privilegesIntoDB();
        //self::privilegesIntoFile();
        //self::groupsIntoFile();
        //self::checkControlPanelAccess();
        //self::processRbacPrivileges();
        //self::createSuperAdmin();
        //self::groupsIntoDB();
        self::checkUrl();
    }

    private static function checkAllPermitions($adminClassName, $adminMethod) {
        $ci = & get_instance();
        $err_text = '<div id="notice" style="width: 500px;">' . '<b>%error_message%.</b>' . '
			</div><script type="text/javascript">showMessage(\'Сообщение: \',\'%error_message%\',\'\');</script>';
        //check if user is loged in
        if ($ci->dx_auth->is_logged_in()) {
            $privilege = $adminClassName . '::' . $adminMethod;
            $privilege = $ci->db->where('name', $privilege)->get(self::$rbac_privileges_table)->row();
            //check if current privilege exist in db
            if ($privilege) {
                $userProfile = $ci->db->where('id', $ci->dx_auth->get_user_id())->get('users')->row();
                if ($userProfile)
                    $userRole = $ci->db->where('id', $userProfile->role_id)->get(self::$rbac_roles_table)->row();
                //check if user has as role
                if ($userRole) {
                    $userPrivilege = $ci->db->where(array('role_id' => (int) $userRole->id, 'privilege_id' => (int) $privilege->id))->get(self::$rbac_roles_privileges_table)->result();
                    if (count($userPrivilege) > 0)
                        return TRUE;
                    else
                        die(str_replace('%error_message%', 'Не достаточно прав для: ' . $privilege->description, $err_text));
                }
            } else {
                return true;
            }
        }
    }

    private static function checkUrl($checkLink = FALSE, $link = '') {
        $ci = & get_instance();

        if ($checkLink AND $link != '') {
            $uri_array = explode("/", $link);
            $for_check = $uri_array[1];
        }
        else
            $for_check = $ci->uri->segment(2);

        if ($for_check == 'components') {
            if ($ci->uri->segment(4) == 'shop' OR $uri_array[3] == 'shop') {
                $classNamePrep = 'ShopAdmin';
                $controller_segment = 5;
                $controller_method = 6;
            }
            if (in_array($ci->uri->segment(3), array('init_window', 'run', 'cp')) OR in_array($uri_array[2], array('init_window', 'run', 'cp'))) {
                $classNamePrep = 'Admin';
                $controller_segment = 4;
                $controller_method = 5;
            } else {
                $controller_segment = 2;
                $controller_method = 3;
                $classNamePrep = 'Base';
            }
        } else {
            $controller_segment = 2;
            $controller_method = 3;
            $classNamePrep = 'Base';
        }
        if ($checkLink AND $link != '')
            $adminController = $uri_array[$controller_segment - 1];
        else
            $adminController = $ci->uri->segment($controller_segment);

        switch ($classNamePrep) {
            case 'ShopAdmin':
                $adminClassName = 'ShopAdmin' . ucfirst($adminController);
                $adminClassFile = self::$shop_controllers_path . $adminController . '.php';
                break;
            case 'Admin':
                $adminClassName = $adminController;
                $adminClassFile = self::$modules_controllers_path . $adminController . '/' . 'admin.php';
                break;
            case 'Base':
                $adminClassName = ucfirst($adminController);
                $adminClassFile = self::$base_controllers_path . $adminController . '.php';
                break;
        }
        if ($checkLink AND $link != '')
            $adminMethod = $uri_array[$controller_method - 1];
        else
            $adminMethod = $ci->uri->segment($controller_method);

        if (!$adminMethod)
            $adminMethod = 'index';

        if (!file_exists($adminClassFile) AND $adminClassFile != 'application/modules/admin/.php')
            die("Файл " . $adminClassFile . " не найден");
        else {
            if ($checkLink AND $link != '')
                return array('adminClassName' => $adminClassName, 'adminMethod' => $adminMethod);
            else
                self::checkAllPermitions($adminClassName, $adminMethod);
        }
    }

    private static function processRbacPrivileges() {
        $ci = & get_instance();
        $controllerFolders = self::$controller_types;
        foreach ($controllerFolders as $folder) {
            if ($folder == 'base') {
                $adminControllersDir = self::$base_controllers_path;
            }
            if ($folder == 'shop') {
                $adminControllersDir = self::$shop_controllers_path;
            }
            if ($folder == 'module') {
                $adminControllersDir = self::$modules_controllers_path;
                $ci->load->helper("directory");
                $controllers = directory_map($adminControllersDir, true);
                foreach ($controllers as $c) {
                    if (file_exists($adminControllersDir . $c . "/admin.php") AND !in_array($c, array('shop', 'admin'))) {
                        $result[] = $adminControllersDir . $c . "/admin.php";
                    }
                }
                $controllers = $result;
            }
            $fileExtension = EXT;

            if ($handle = opendir($adminControllersDir)) {
                //list of the admin controllers
                if (!$controllers)
                    $controllers = glob($adminControllersDir . "*$fileExtension");
                foreach ($controllers as $controller) {
                    self::scanControllers($controller, $folder);
                }
                $controllers = false;
                closedir($handle);
            }
        }
        showMessage("Успех");
    }

    private static function scanControllers($controller, $folder) {
        $locale = BaseAdminController::getCurrentLocale();
        $ci = & get_instance();
        $fileExtension = EXT;
        if ($folder == 'module') {
            $arr = explode("/", $controller);
            $text = file_get_contents($controller);
            $text = str_replace("class Admin", "class " . $arr[2], $text);
            write_file(str_replace("admin.php", $arr[2] . "temp" . $fileExtension, $controller), $text);
            $controller = str_replace("admin.php", $arr[2] . "temp" . $fileExtension, $controller);
        }

        require_once $controller;
        $controllerName = str_replace("temp", "", basename($controller, $fileExtension));
        switch ($folder) {
            case 'base':
                $controllerClassName = ucfirst($controllerName);
                break;
            case 'module':
                $controllerClassName = $arr[2];
                break;
            case 'shop':
                $controllerClassName = 'ShopAdmin' . ucfirst($controllerName);
                break;
        }

        $class = new ReflectionClass($controllerClassName);

        $controllerMethods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($controllerMethods as $controllerMethod) {
            if ($controllerMethod->class == $controllerClassName) {
                $privilegeName = $controllerMethod->class . '::' . $controllerMethod->name;
                $dbPrivilege = $ci->db->where('name', $privilegeName)->get(self::$rbac_privileges_table)->row();
                $group = $ci->db->where('name', ucfirst($controllerClassName))->get(self::$rbac_group_table)->row();
                if (empty($group)) {
                    $ci->db->insert(self::$rbac_group_table, array('name' => ucfirst($controllerClassName), 'type' => $folder));
                    $ci->db->insert(self::$rbac_group_table . "_i18n", array('id' => $ci->db->insert_id(), 'description' => '', 'locale' => $locale));
                    $group = $ci->db->where('name', ucfirst($controllerName))->get(self::$rbac_group_table)->row();
                }
                if (empty($dbPrivilege)) {
                    $ci->db->insert(self::$rbac_privileges_table, array('name' => $privilegeName, 'group_id' => $group->id));
                    $ci->db->insert(self::$rbac_privileges_table . "_i18n", array('id' => $ci->db->insert_id(), 'title' => $privilegeName, 'description' => '', 'locale' => $locale));
                }
            }
        }
        if ($folder == 'module')
            unlink($controller);
    }

    private static function checkSuperAdmin() {
        $ci = & get_instance();
        $superAdmin = $ci->db->where('id', 1)->get('users')->row();
        if (empty($superAdmin))
            die("Супер администратор не найден");
        else {
            $role_id = $superAdmin->role_id;
            $privileges = $ci->db->get(self::$rbac_privileges_table)->result();
            if (!empty($privileges)) {
                $countAllPermitions = count($privileges);
                $countUserPermitions = 0;
                foreach ($privileges as $privilege) {
                    if ($ci->db->where(array('privilege_id' => $privilege->id, 'role_id' => $role_id))->get(self::$rbac_roles_privileges_table)->num_rows() > 0)
                        $countUserPermitions++;
                }
                if ($countAllPermitions == $countUserPermitions)
                    return true;
                else
                    die("Суперадмин не найден");
            }
        }
    }

    private static function createSuperAdmin() {
        $ci = & get_instance();
        $superAdmin = $ci->db->where('id', 1)->get('users')->row();
        if (empty($superAdmin))
            die("Супер администратор не найден");
        else {
            $role_id = $superAdmin->role_id;
            $privileges = $ci->db->get(self::$rbac_privileges_table)->result();
            if (!empty($privileges))
                foreach ($privileges as $privilege) {
                    if ($ci->db->where(array('privilege_id' => $privilege->id, 'role_id' => $role_id))->get(self::$rbac_roles_privileges_table)->num_rows() == 0)
                        $ci->db->insert(self::$rbac_roles_privileges_table, array('role_id' => $role_id, 'privilege_id' => $privilege->id));
                }
        }
    }

    private function lang() {
        $sqlLangSel = 'SELECT lang_sel FROM settings';
        $lang = $this->db->query($sqlLangSel)->row();
        return $lang->lang_sel;
    }

    /*     * *************  RBAC privileges groups  ************** */

    /**
     * create a RBAC privileges group
     * 
     * @access public 
     * @return	void
     */
    public function groupCreate() {

        $this->form_validation->set_rules('Name', 'Name', 'required');

        if (!empty($_POST)) {
            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $sql = "INSERT INTO shop_rbac_group (type, name) VALUES(" . $this->db->escape($_POST['type']) . "," . $this->db->escape($_POST['Name']) . ")";
                $this->db->query($sql);

                $idNewGroup = $this->db->insert_id();

                $sql = "INSERT INTO  shop_rbac_group_i18n (id, description, locale) VALUES(" . $idNewGroup . ", " . $this->db->escape($_POST['Description']) . ", '" . BaseAdminController::getCurrentLocale() . "' ) ";
                $this->db->query($sql);

                if ($_POST['Privileges']) {
                    $idPrivilege = implode(',', $_POST['Privileges']);
                    $sql = "UPDATE shop_rbac_privileges SET group_id = " . $idNewGroup . " WHERE id IN(" . $idPrivilege . ")";
                    $this->db->query($sql);
                }

                showMessage('Группа создана');
                if ($_POST['action'] == 'tomain')
                    pjax('/admin/rbac/groupEdit/' . $idNewGroup);
                if ($_POST['action'] == 'tocreate')
                    pjax('/admin/rbac/groupCreate');
                if ($_POST['action'] == 'toedit')
                    pjax('/admin/rbac/groupEdit/' . $idNewGroup);
            }
        } else {

            $sqlModel = 'SELECT SRP.id, SRP.name, SRP.group_id, SRPI.title, SRPI.description 
            FROM shop_rbac_privileges SRP
            INNER JOIN shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '"  ORDER BY SRP.name ASC';
            $model = $this->db->query($sqlModel);

            $this->template->add_array(array(
                'model' => $model,
                'privileges' => $model->result(),
            ));

            $this->template->show('groupCreate', FALSE);
        }
    }

    public function translateGroup($id, $lang) {
        $sqlModel = 'SELECT description 
            FROM shop_rbac_group_i18n WHERE id =' . $id . ' AND locale = "' . $lang . '"';
        $model = $this->db->query($sqlModel)->row();


        if ($_POST) {
            if (empty($model)) {

                $sql = "INSERT INTO  shop_rbac_group_i18n (id, description, locale) VALUES(" . $id . ", " . $this->db->escape($_POST['Description']) . ", '" . $lang . "' ) ";
                $this->db->query($sql);
            } else {

                $sql = "UPDATE shop_rbac_group_i18n SET description = " . $this->db->escape($_POST['Description']) . " WHERE id = " . $id . " AND locale = '" . $lang . "'";
                $this->db->query($sql);
            }
            if ($_POST['action'] == 'tomain') {
                pjax('/admin/rbac/translateGroup/' . $id . '/' . $lang);
            } else {
                pjax('/admin/rbac/groupEdit/' . $id);
            }
        } else {
            $this->template->add_array(array(
                'model' => $model,
                'id' => $id,
                'lang_sel' => $lang
            ));

            $this->template->show('translateGroup', FALSE);
        }
    }

    public function groupEdit($groupId) {

        $sqlModel = 'SELECT SRG.id, SRG.name, SRGI.description 
            FROM shop_rbac_group SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRG.id = "' . $groupId . '" AND SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"  ORDER BY SRG.name ASC';
        $model = $this->db->query($sqlModel);

        if ($model === null)
            $this->error404('Группа не найдена');

        if (!empty($_POST)) {

            $sql = "UPDATE shop_rbac_group SET name = " . $this->db->escape($_POST['Name']) .
                    " WHERE id = " . $groupId;
            $this->db->query($sql);

            $sql = "UPDATE shop_rbac_group_i18n SET description = " . $this->db->escape($_POST['Description']) . " WHERE id = " . $groupId . " AND locale = '" . BaseAdminController::getCurrentLocale() . "'";
            $this->db->query($sql);

            if ($_POST['Privileges']) {
                $idPrivilege = implode(',', $_POST['Privileges']);
                $sql = "UPDATE shop_rbac_privileges SET group_id = " . $groupId . " WHERE id IN(" . $idPrivilege . ")";
                $this->db->query($sql);
            }
            showMessage('Изменения сохранены');
            if ($_POST['action'] == 'tomain')
                pjax('/admin/rbac/groupEdit/' . $groupId);
            if ($_POST['action'] == 'tocreate')
                pjax('/admin/rbac/groupCreate');
            if ($_POST['action'] == 'toedit')
                pjax('/admin/rbac/groupEdit/' . $groupId);
        } else {

//            $sqlPrivilege = $this->db->select(array('id', 'name', 'group_id'))->get('shop_rbac_privileges')->result();
            $sqlPrivilege = 'SELECT SRP.id, SRP.name, SRP.group_id, SRPI.title, SRPI.description 
            FROM shop_rbac_privileges SRP
            INNER JOIN  shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '"  ORDER BY SRPI.title ASC';
            $sqlPrivilege = $this->db->query($sqlPrivilege)->result();


            $this->template->add_array(array(
                'model' => $model->row(),
                'lang_sel' => self::lang(),
                'privileges' => $sqlPrivilege
            ));

            $this->template->show('groupEdit', FALSE);
        }
    }

    public function groupList() {


        $sql = 'SELECT SRG.id, SRG.name, SRGI.description 
            FROM shop_rbac_group SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '" ORDER BY name ASC';
        $query = $this->db->query($sql);

        $this->template->add_array(array(
            'model' => $query->result()
        ));

        $this->template->show('groupList', FALSE);
    }

    /**
     * delete a RBAC privileges group
     * 
     * @param integer $groupId
     * @access public 
     * @return	void
     */
    public function groupDelete() {
        $groupId = $this->input->post('ids');

        if ($groupId != null) {
            foreach ($groupId as $id) {
                $this->db->delete('shop_rbac_group', array('id' => $id));
                $this->db->delete('shop_rbac_group_i18n', array('id' => $id));
            }
            showMessage('Успех', 'Группа(ы) успешно удалены');
            pjax('/admin/rbac/groupList');
        }
    }

    /*     * *************  RBAC roles  ************** */

    /**
     * create a RBAC role
     * 
     * @access public 
     * @return     void
     */
    public function roleCreate() {

        if (!empty($_POST)) {
            $this->form_validation->set_rules('Name', 'Name', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {


                $sql = "INSERT INTO shop_rbac_roles(name, importance) VALUES(" . $this->db->escape($_POST['Name']) . ", " . $this->db->escape($_POST['Importance']) .
                        ")";
                $this->db->query($sql);
                $idCreate = $this->db->insert_id();

                $sql = "INSERT INTO shop_rbac_roles_i18n(id, alt_name, locale, description) VALUES(" . $idCreate . ", " . $this->db->escape($_POST['Name']) .
                        ",  '" . BaseAdminController::getCurrentLocale() . "',  "
                        . $this->db->escape($_POST['Description']) . ")";
                $this->db->query($sql);


                if ($_POST['Privileges']) {


                    foreach ($_POST['Privileges'] as $idPrivilege) {
                        $sqlPrivilege = "INSERT INTO shop_rbac_roles_privileges (role_id, privilege_id) VALUES(" . $idCreate . ", " . $this->db->escape($idPrivilege) . ")";
                        $this->db->query($sqlPrivilege);
                    }
                }


                showMessage(lang('a_js_edit_save'));

                if ($_POST['action'] == 'new') {
                    pjax('/admin/rbac/roleEdit/' . $idCreate);
                } else {
                    pjax('/admin/rbac/roleList');
                }
            }
        } else {

            $queryGroups = $this->db->select(array('id', 'name'))->get('shop_rbac_group')->result();
            foreach ($queryGroups as $key => $value) {
                $queryGroups[$key]->privileges = $this->db->get_where('shop_rbac_privileges', array('group_id' => $value->id))->result();
            }

            $array = $this->db->select('type')->distinct()->get('shop_rbac_group')->result();

            foreach ($array as $item) {
                $result[$item->type] = $this->db->where('type', $item->type)->get(self::$rbac_group_table)->result_array();
                foreach ($result[$item->type] as $k => $v) {
                    $result[$item->type][$k]['privileges'] = $this->db->get_where('shop_rbac_privileges', array('group_id' => $v['id']))->result();
                }
            }

            $this->template->add_array(array(
                'groups' => $result
            ));

            $this->template->show('roleCreate', FALSE);
        }
    }

    public function translateRole($id, $lang) {

        $sqlModel = 'SELECT id, alt_name, locale, description
            FROM  shop_rbac_roles_i18n
            WHERE id = "' . $id . '" AND locale = "' . $lang . '"';

        $queryModel = $this->db->query($sqlModel)->row();

        if ($_POST) {
            if (empty($queryModel)) {

                $sql = "INSERT INTO shop_rbac_roles_i18n(id, alt_name, locale, description) VALUES(" . $id . ", " . $this->db->escape($_POST['alt_name']) .
                        ",  '" . $lang . "',  "
                        . $this->db->escape($_POST['Description']) . ")";
                $this->db->query($sql);
            } else {
                $sqlI = "UPDATE shop_rbac_roles_i18n SET alt_name = " . $this->db->escape($_POST['alt_name']) . ", locale = '" . $lang . "', description = " . $this->db->escape($_POST['Description']) . " WHERE id = '" . $id . "' AND locale = '" . $lang . "'";
                $this->db->query($sqlI);
            }

            showMessage(lang('a_js_edit_save'));
            if ($_POST['action'] == 'edit') {
                pjax('/admin/rbac/translateRole/' . $id . '/' . $lang);
            } else {
                pjax('/admin/rbac/roleList');
            }
        } else {

            $this->template->add_array(array(
                'model' => $queryModel,
                'idRole' => $id,
                'lang_sel' => $lang
            ));
            $this->template->show('translateRole', FALSE);
        }
    }

    /**
     * edit a RBAC role
     *
     * @access	public 
     * @param	integer $roleId
     * @return	void
     */
    public function roleEdit($roleId) {

        $sqlModel = 'SELECT SRR.id, SRR.name, SRR.importance, SRRI.alt_name, SRRI.description
            FROM shop_rbac_roles SRR
            INNER JOIN shop_rbac_roles_i18n SRRI ON SRRI.id = SRR.id WHERE SRR.id = "' . $roleId . '" AND SRRI.locale = "' . BaseAdminController::getCurrentLocale() . '" ORDER BY SRR.name ASC';

        $queryModel = $this->db->query($sqlModel);
        $queryModel->row();

        if ($queryModel === null)
            $this->error404(lang('a_rback_not_found'));

        if (!empty($_POST)) {
            $this->form_validation->set_rules('Name', 'Name', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {


                $sql = "UPDATE shop_rbac_roles SET importance = " . $this->db->escape($_POST['Importance']) .
                        " WHERE id   =   '" . $roleId . "'";
                $this->db->query($sql);

                $sqlI = "UPDATE shop_rbac_roles_i18n SET alt_name = " . $this->db->escape($_POST['Name']) . ", description = " . $this->db->escape($_POST['Description']) . " WHERE id = '" . $roleId . "' AND locale = '" . BaseAdminController::getCurrentLocale() . "'";
                $this->db->query($sqlI);


                if ($_POST['Privileges']) {
                    $idForDelete = implode(',', $_POST['Privileges']);


                    $sqlDelete = "DELETE FROM shop_rbac_roles_privileges WHERE role_id = " . $roleId . " AND privilege_id NOT IN(" . $idForDelete . ")";
                    $this->db->query($sqlDelete);

                    foreach ($_POST['Privileges'] as $idPrivilege) {

                        $sqlPrivilege = "INSERT INTO shop_rbac_roles_privileges (role_id, privilege_id) VALUES(" . $this->db->escape($roleId) . ", " . $this->db->escape($idPrivilege) . ")";
                        $this->db->query($sqlPrivilege);
                    }
                }

                showMessage(lang('a_js_edit_save'));

                if ($_POST['action'] == 'edit') {

                    pjax('/admin/rbac/roleEdit/' . $roleId);
                } else {
                    pjax('/admin/rbac/roleList');
                }
            }
        } else {

            $sql = 'SELECT id, role_id, privilege_id
            FROM shop_rbac_roles_privileges WHERE role_id = ' . $roleId;
            $queryPrivilegeR = $this->db->query($sql)->result_array();

            $sqlGroup = 'SELECT SRG.id, SRG.name, SRGI.description
            FROM shop_rbac_group SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"';
            $queryGroups = $this->db->query($sqlGroup)->result();

            foreach ($queryGroups as $key => $value) {

                $sqlPrivilegeS = 'SELECT SRP.id, SRP.name, SRPI.title, SRPI.description  
            FROM shop_rbac_privileges SRP
            INNER JOIN shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '" AND SRP.group_id = ' . $value->id;
                $queryGroups = $this->db->query($sqlPrivilegeS)->result();
                $queryGroups[$key]->privileges = $queryGroups;
            }
            $emptyArray = array();

            foreach ($queryPrivilegeR as $key => $id) {
                $emptyArray[$key] = $id['privilege_id'];
            }

            $sqlGroupAr = 'SELECT SRG.type, SRGI.description
            FROM shop_rbac_group SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"';
            $array = $this->db->query($sqlGroupAr)->result();

            foreach ($array as $item) {

                $sqlGroupArTh = 'SELECT SRG.id, SRG.type, SRG.name, SRGI.description
            FROM ' . self::$rbac_group_table . ' SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '" AND SRG.type = "' . $item->type . '"';
                $result[$item->type] = $this->db->query($sqlGroupArTh)->result_array();

                foreach ($result[$item->type] as $k => $v) {

                    $sqlGroupArF = 'SELECT SRP.id, SRP.name, SRPI.title, SRPI.description
            FROM shop_rbac_privileges SRP
            INNER JOIN shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '" AND SRP.group_id = "' . $v['id'] . '"';

                    $result[$item->type][$k]['privileges'] = $this->db->query($sqlGroupArF)->result();
                }
            }

            $this->template->add_array(array(
                'model' => $queryModel->row(),
                'groups' => $queryGroups,
                'privilegeCheck' => $emptyArray,
                'lang_sel' => self::lang(),
                'types' => $result
            ));

            $this->template->show('roleEdit', FALSE);
        }
    }

    /**
     * display a list of RBAC roles
     * 
     * @access public
     * @return	void
     */
    public function roleList() {

        $sql = 'SELECT SRR.id, SRR.name, SRR.importance, SRRI.alt_name, SRRI.description  
            FROM shop_rbac_roles SRR
            INNER JOIN shop_rbac_roles_i18n SRRI ON SRRI.id = SRR.id WHERE SRRI.locale = "' . BaseAdminController::getCurrentLocale() . '" ORDER BY SRR.name ASC';
        $query = $this->db->query($sql);

        $this->template->add_array(array(
            'model' => $query->result(),
        ));

        $this->template->show('roleList', FALSE);
    }

    /**
     * delete a RBAC privileges group
     * 
     * @param integer $groupId
     * @access public 
     * @return	void
     */
    public function roleDelete() {
        $groupId = $this->input->post('ids');

        if ($groupId != null) {
            foreach ($groupId as $id) {
                $this->db->delete('shop_rbac_roles', array('id' => $id));
                $this->db->delete('shop_rbac_roles_i18n', array('id' => $id));
                $this->db->delete('shop_rbac_roles_privileges', array('role_id' => $id));
            }

            showMessage('Успех', 'Группа(ы) успешно удалены');
            pjax('/admin/rbac/roleList');
        }
    }

    /*     * *************  RBAC privileges  ************** */

    /**
     * create a RBAC privilege
     * 
     * @access public 
     * @return	void
     */
    public function privilegeCreate() {

        if (!empty($_POST)) {

            $this->form_validation->set_rules('Name', 'Name', 'required');

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {


                $sql = "INSERT INTO shop_rbac_privileges(name, group_id) VALUES(" . $this->db->escape($_POST['Name']) .
                        ",  " . $this->db->escape($_POST['GroupId']) . ")";
                $this->db->query($sql);

                $idNewPrivilege = $this->db->insert_id();

                $sqlI = "INSERT INTO shop_rbac_privileges_i18n(id, title, description, locale) VALUES("
                        . $idNewPrivilege .
                        ", " . $this->db->escape($_POST['Title']) .
                        ", " . $this->db->escape($_POST['Description']) .
                        ", '" . BaseAdminController::getCurrentLocale() . "')";
                $this->db->query($sqlI);


                showMessage(lang('a_rbak_privile_create'));

                if ($_POST['action'] == 'close') {
                    pjax('/admin/rbac/privilegeEdit/' . $idNewPrivilege);
                } else {
                    pjax('/admin/rbac/privilegeList');
                }
            }
        } else {
            $sql = 'SELECT SRG.id, SRGI.description  
            FROM shop_rbac_group SRG
            INNER JOIN  shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"';
            $queryRBACGroup = $this->db->query($sql)->result();

            $this->template->add_array(array(
                'groups' => $queryRBACGroup
            ));

            $this->template->show('privilegeCreate', FALSE);
        }
    }

    public function translatePrivilege($id, $lang) {

        $sqlPr = 'SELECT SRP.id, SRP.name, SRP.group_id, SRPI.title, SRPI.description  
            FROM shop_rbac_privileges SRP
            INNER JOIN   shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . $lang . '" AND SRP.id = ' . $id;

        $queryRBACPrivilege = $this->db->query($sqlPr)->row();

        if ($_POST) {
            if (empty($queryRBACPrivilege)) {

                $sqlI = "INSERT INTO shop_rbac_privileges_i18n(id, title, description, locale) VALUES("
                        . $id .
                        ", " . $this->db->escape($_POST['Title']) .
                        ", " . $this->db->escape($_POST['Description']) .
                        ", '" . $lang . "')";
                $this->db->query($sqlI);
            } else {
                $sqlI = "UPDATE shop_rbac_privileges_i18n SET title = " . $this->db->escape($_POST['Title']) . ",  description  =  " . $this->db->escape($_POST['Description']) . " WHERE id = " . $id . ' AND locale = "' . $lang . '"';
                $this->db->query($sqlI);
            }
            if ($_POST['action'] == 'close') {
                pjax('/admin/rbac/translatePrivilege/' . $id . '/' . $lang);
            } else {
                pjax('/admin/rbac/privilegeEdit/' . $id);
            }
        } else {


            $this->template->add_array(array(
                'model' => $queryRBACPrivilege,
                'idRole' => $id,
                'lang_sel' => $lang
            ));

            $this->template->show('translatePrivilege', FALSE);
        }
    }

    /**
     * edit a RBAC privilege
     * 
     * @param integer $privilegeId
     * @access public 
     * @return	void
     */
    public function privilegeEdit($privilegeId) {
        $sqlPr = 'SELECT SRP.id, SRP.name, SRP.group_id, SRPI.title, SRPI.description  
            FROM shop_rbac_privileges SRP
            INNER JOIN   shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '" AND SRP.id = ' . $privilegeId;

        $queryRBACPrivilege = $this->db->query($sqlPr)->row();

        if ($queryRBACPrivilege === null AND FALSE)
            $this->error404(lang('a_rbak_privi_not'));


        if (!empty($_POST)) {

            $sql = "UPDATE shop_rbac_privileges SET name = " . $this->db->escape($_POST['Name']) . ", group_id = " . $this->db->escape($_POST['GroupId']) .
                    " WHERE id = " . $privilegeId;
            $this->db->query($sql);

            $sqlI = "UPDATE shop_rbac_privileges_i18n SET title = " . $this->db->escape($_POST['Title']) . ",  description  =  " . $this->db->escape($_POST['Description']) . " WHERE id = " . $privilegeId . ' AND locale = "' . BaseAdminController::getCurrentLocale() . '"';
            $this->db->query($sqlI);

            showMessage(lang('a_js_edit_save'));

            if ($_POST['action'] == 'close') {
                pjax('/admin/rbac/privilegeEdit/' . $privilegeId);
            } else {
                pjax('/admin/rbac/privilegeList');
            }
        } else {
            $sql = 'SELECT SRG.id, SRGI.description  
            FROM shop_rbac_group SRG
            INNER JOIN  shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"';
            $queryRBACGroup = $this->db->query($sql)->result();

            $this->template->add_array(array(
                'model' => $queryRBACPrivilege,
                'lang_sel' => self::lang(),
                'groups' => $queryRBACGroup
            ));

            $this->template->show('privilegeEdit', FALSE);
        }
    }

    /**
     * display a list of RBAC privileges
     * 
     * @access public
     * @return	void
     */
    public function privilegeList() {

        $sql = 'SELECT SRG.id, SRG.name, SRGI.description  
            FROM shop_rbac_group SRG
            INNER JOIN shop_rbac_group_i18n SRGI ON SRGI.id = SRG.id WHERE SRGI.locale = "' . BaseAdminController::getCurrentLocale() . '"';
        $queryGroups = $this->db->query($sql)->result();
        foreach ($queryGroups as $key => $value) {
            $sqlPriv = 'SELECT SRP.id, SRP.name, SRP.group_id, SRPI.title, SRPI.description  
            FROM shop_rbac_privileges SRP
            INNER JOIN  shop_rbac_privileges_i18n SRPI ON SRPI.id = SRP.id WHERE SRPI.locale = "' . BaseAdminController::getCurrentLocale() . '" AND SRP.group_id = ' . $value->id;
            $queryGroupsPrivilege = $this->db->query($sqlPriv)->result();

            $queryGroups[$key]->privileges = $queryGroupsPrivilege;
        }

        $queryRBACGroup = $this->db->select(array('id', 'name'))->get('shop_rbac_privileges')->result();

        $this->template->add_array(array(
            'model' => $queryRBACGroup,
            'groups' => $queryGroups
        ));

        $this->template->show('privilegeList', FALSE);
    }

    /**
     * delete a RBAC privilege
     * 
     * @param integer $privilegeId
     * @access public 
     * @return	void
     */
    public function privilegeDelete() {
        $privilegeId = $this->input->post('id');
        $model = ShopRbacPrivilegesQuery::create()
                ->findPks($privilegeId);

        if ($model != null) {
            $model->delete();
            showMessage('Успех', 'Привилегии успешно удалены');
            pjax('/admin/components/run/shop/rbac/privilege_list');
        }
    }

    public static function checkControlPanelAccess($role_id) {
        if ($role_id != null) {
            $ci = & get_instance();
            $r = $ci->db->query("SELECT * FROM `" . self::$rbac_roles_privileges_table . "`
                        JOIN `" . self::$rbac_privileges_table . "` ON " . self::$rbac_roles_privileges_table . ".privilege_id = " . self::$rbac_privileges_table . ".id
                        WHERE " . self::$rbac_roles_privileges_table . ".role_id = " . $role_id . " AND `name` = 'Admin::__construct'")->num_rows();
            if ($r
                    > 0)
                return 'admin';
            else
                return '';
        }
        else
            return '';
    }

    /* private static function groupsIntoFile() {
      $ci = &get_instance();
      $join_string = self::$rbac_group_table . ".id=" . self::$rbac_group_table . "_i18n.id";
      $groups = $ci->db->query("SELECT * FROM `" . self::$rbac_group_table . "`
      JOIN `" . self::$rbac_group_table . "_i18n` ON " . $join_string)->result_array();
      file_put_contents('groups.php', var_export($groups, true));
      }

      private static function groupsIntoDB() {
      $ci = &get_instance();
      $locale = 'ru';
      $string = "\$result = " . file_get_contents('groups_descriptions.php') . ";";
      eval($string);
      if (is_array($result)) {
      foreach ($result as $item) {
      $ci->db->where('id', $item['id'])->update(self::$rbac_group_table . "_i18n", array('description' => $item['description']));
      }
      }
      }

      private static function privilegesIntoFile() {
      $ci = &get_instance();
      $locale = 'ru';
      $join_string = self::$rbac_privileges_table . ".id=" . self::$rbac_privileges_table . "_i18n.id";
      $privileges = $ci->db->query("SELECT * FROM `" . self::$rbac_privileges_table . "`
      JOIN `" . self::$rbac_privileges_table . "_i18n` ON " . $join_string)->result_array();
      file_put_contents('privileges.php', var_export($privileges, true));
      }

      private static function privilegesIntoDB() {
      $ci = &get_instance();
      $locale = 'ru';
      $string = "\$result = " . file_get_contents('privileges.php') . ";";
      eval($string);
      //        var_dump($result);
      //        exit();
      if (is_array($result)) {
      foreach ($result as $item) {
      $ci->db->where('id', $item['id'])->update(self::$rbac_privileges_table . "_i18n", array('title' => $item['title'], 'description' => $item['description']));
      }
      }
      } */
}

?>
