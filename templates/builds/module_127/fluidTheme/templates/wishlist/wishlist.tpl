{$pricePrecision = ShopCore::app()->SSettings->pricePrecision}
<div class="frame-inside page-wishlist">
    <div class="container">
        <div class="f-s_0 title-cart without-crumbs">
            <div class="frame-title">
                <h1 class="title">{lang('Список желаний','newLevel')}</h1>
            </div>
        </div>
        {if $errors}
            {foreach $errors as $error}
                <div class="msg">
                    <div class="error">{$error}</div>
                </div>
            {/foreach}
        {/if}
        <div class="clearfix">
            <div class="frame-button-add-wish-list">
                <div class="btn-def">
                    <button type="button" data-drop=".drop-add-wishlist" data-place="inherit" data-overlay-opacity="0" data-effect-on="slideDown" data-effect-off="slideUp">
                        <span class="icon_add_wish"></span>
                        <span class="text-el">{lang('Создать новый список','newLevel')}</span>
                    </button>
                </div>
                <span>{lang('В список избранных вы можете отложить понравившиеся товары, также показать список друзьям', 'newLevel')}</span>
            </div>
            <div class="drop drop-style-2 drop-add-wishlist">
                <div class="drop-header">
                    <div class="title">{lang('Создание списка избранных товаров','newLevel')}</div>
                </div>
                <div class="drop-content2">
                    <div class="inside-padd">
                        <div class="horizontal-form big-title">
                            <form method="POST" action="{site_url('/wishlist/wishlistApi/createWishList')}">
                                <input type="hidden" value="{echo $user[id]}" name="user_id"/>
                                <div class="frame-label">
                                    <span class="title">{lang('Доступность:','newLevel')}</span>
                                    <div class="frame-form-field check-public">
                                        <div class="lineForm">
                                            <select name="wlTypes" id="wlTypes">
                                                <option value="shared">{lang('Коллективный', 'newLevel')}</option>
                                                <option value="public">{lang('Публичный', 'newLevel')}</option>
                                                <option value="private">{lang('Приватный', 'newLevel')}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <label>
                                    <span class="title">{lang('Название списка:','newLevel')}</span>
                                    <span class="frame-form-field">
                                        <input type="text" value="" name="wishListName"/>
                                    </span>
                                </label>
                                <label>
                                    <span class="title">{lang('Описание:','newLevel')}</span>
                                    <span class="frame-form-field">
                                        <textarea name="wlDescription"></textarea>
                                    </span>
                                </label>
                                <div class="frame-label">
                                    <span class="title">&nbsp;</span>
                                    <div class="frame-form-field">
                                        <div class="btn-def">
                                            <button
                                                type="submit"
                                                data-source="{site_url('/wishlist/wishlistApi/createWishList')}"
                                                data-modal="true"

                                                data-always="true"
                                                onclick="serializeForm(this)"
                                                data-drop="#notification"
                                                data-effect-on="fadeIn"
                                                data-effect-off="fadeOut"
                                                data-after="WishListFront.createWishList"
                                                >
                                                <span class="text-el">{lang('Создать новый список','newLevel')}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {form_csrf()}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {if count($wishlists)>0}
                {foreach $wishlists as $key => $wishlist}
                    <div class="drop-style-2 drop-wishlist-items" data-rel="list-item">
                        <input type="hidden" name="WLID" value="{echo $wishlist[0][wish_list_id]}">
                        <div class="drop-content2">
                            <div class="inside-padd">
                                {if $wishlist[0][title]}
                                    <h2>{$wishlist[0][title]}</h2>
                                {/if}
                                {if $wishlist[0][description]}
                                    <div class="text">
                                        {$wishlist[0][description]}
                                    </div>
                                {/if}
                                {if $wishlist[0][variant_id]}
                                    <ul class="items items-catalog items-wish-list items-product">
                                        {$CI->load->module('new_level')->OPI($wishlist, array('opi_wishListPage' => true))}
                                    </ul>
                                {else:}
                                    <div class="msg layout-highlight layout-highlight-msg">
                                        <div class="info">
                                            <span class="icon_info"></span>
                                            <span class="text-el">{lang('Список пуст','newLevel')}</span>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="drop-footer2">
                            <div class="inside-padd clearfix">
                                <div class="funcs-buttons-wishlist d_i-b">
                                    <div class="btn-remove-WL">
                                        <button
                                            type="button"
                                            data-source="{site_url('/wishlist/wishlistApi/deleteWL/'.$wishlist[0][wish_list_id])}"
                                            data-modal="true"

                                            data-drop="#notification"
                                            data-after="WishListFront.removeWL"
                                            data-confirm="true"

                                            data-effect-on="fadeIn"
                                            data-effect-off="fadeOut"
                                            >
                                            <span class="icon_remove"></span>
                                            <span class="text-el d_l_1">{lang('Удалить весь список','newLevel')}</span>
                                        </button>
                                    </div>
                                    <div class="btn-edit-WL">
                                        <button
                                            type="button"
                                            data-source="{site_url('/wishlist/editWL/'.$wishlist[0][wish_list_id])}"
                                            data-drop=".drop-edit-wishlist"
                                            data-always="true"
                                            >
                                            <span class="d_l_1 text-el">{lang('Редактировать список','newLevel')}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="d_i-b">
                                    <span class="m-r_30 d_i-b">
                                        <b>{lang('Доступность:','newLevel')}</b>
                                        {if $wishlist[0][access] == 'private'}
                                            <span class="s_t">{lang('Приватный', 'newLevel')}</span>
                                        {/if}
                                        {if $wishlist[0][access] == 'public'}
                                            <span class="s_t">{lang('Публичный', 'newLevel')}</span>
                                        {/if}
                                        {if $wishlist[0][access] == 'shared'}
                                            <span class="s_t">{lang('Коллективный', 'newLevel')}</span>
                                        {/if}
                                        <span class="m-r_20"></span>
                                        <span class="d_i-b">
                                            {if $wishlist[0]['access'] == 'shared'}
                                                {echo $CI->load->module('share')->_make_share_form(site_url('wishlist/show/'.$wishlist[0]['hash']))}
                                            {/if}
                                        </span>
                                    </span>
                                    {if $wishlist[0]['access'] == 'shared' || $wishlist[0]['access'] == 'public'}
                                        <div class="btn-form btn-send-wishlist">
                                            <button type="button" data-drop=".drop-sendemail" title="{lang('Поделится с другом','newLevel')}" data-source="{echo site_url('wishlist/wishlistApi/renderEmail/' . $wishlist[0][wish_list_id])}">
                                                <span class="icon_mail"></span>
                                                <span class="text-el">{lang('Поделиться с другом')}</span>
                                            </button>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {form_csrf()}
                    </div>
                {/foreach}
            {else:}
                <div class="msg layout-highlight layout-highlight-msg">
                    <div class="info">
                        <span class="icon_info"></span>
                        <span class="text-el">{lang('Список Желания пуст','newLevel')}</span>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>