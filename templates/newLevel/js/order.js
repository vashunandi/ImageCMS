if(selectDeliv){var methodDeliv="#method_deliv"}else{var methodDeliv='[name = "deliveryMethodId"]'}function renderOrderDetails(){$(genObj.orderDetails).html(_.template($(genObj.orderDetailsTemplate).html(),{cart:Shop.Cart}));$(document).trigger({type:"renderorder.after",el:$(genObj.orderDetails)});initShopPage(false);recountCartPage()}function changeDeliveryMethod(a){$(genObj.pM).next().show();$.get("/shop/cart_api/getPaymentsMethods/"+a,function(b){var d=JSON.parse(b),c="";if(selectPayment){c=_.template('<div class="lineForm"><select id="paymentMethod" name="paymentMethodId"><% _.each(data, function(item) { %><option value="<%-item.id%>"><%-item.name%></option> <% }) %></select></div>',{data:d})}else{c=_.template('<div class="frame-radio"><% var i=0 %><% _.each(data, function(item) { %> <div class="frame-label"><span class = "niceRadio b_n"><input type = "radio" name = "paymentMethodId" value = "<%-item.id%>" <% if (i == 0){ %>checked = "checked"<% i++} %> /></span><div class = "name-count"><span class = "text-el"><%-item.name%></span></div><div class="help-block"><%=item.description%></div></div> <% }) %></div>',{data:d})}$(genObj.pM).html(c);$(genObj.pM).next().hide();if(selectPayment){cuselInit($(genObj.pM),"#paymentMethod")}else{$(genObj.pM).nStRadio({wrapper:$(".frame-radio > .frame-label"),elCheckWrap:".niceRadio"})}})}function displayOrderSum(c){var f=Shop.Cart.discount,e=parseFloat(Shop.Cart.kitDiscount),a=Shop.Cart.getFinalAmount();if(Shop.Cart.koefCurr==undefined){var b=parseFloat(Shop.Cart.totalPrice).toFixed(pricePrecision),d=parseFloat(Shop.Cart.totalAddPrice).toFixed(pricePrecision);Shop.Cart.koefCurr=d/b}if(f!=null&&f!=0){a=a-f.result_sum_discount_convert}if(e!=0){a=a-e}a=a>Shop.Cart.shipping?a:Shop.Cart.shipping;$(genObj.totalPrice).html(parseFloat(Shop.Cart.getTotalPriceOrigin()).toFixed(pricePrecision));$(genObj.finalAmount).html(parseFloat(a).toFixed(pricePrecision));$(genObj.finalAmountAdd).html((Shop.Cart.koefCurr*a).toFixed(pricePrecision));$(genObj.shipping).html(parseFloat(Shop.Cart.shipping).toFixed(pricePrecision));if(c!=null){if(parseFloat(c.result_sum_discount_convert)>0){$(genObj.frameGenDiscount).show()}else{$(genObj.frameGenDiscount).hide()}}else{$(genObj.frameGenDiscount).hide()}}function recountCartPage(){Shop.Cart.totalRecount();var a="";if(selectDeliv){a=$(genObj.frameDelivery).find("span.cuselActive")}else{a=$(methodDeliv).filter(":checked")}Shop.Cart.shipping=parseFloat(a.data("price"));Shop.Cart.shipFreeFrom=parseFloat(a.data("freefrom"));if($.isFunction(window.loadCertificat)){loadCertificat(Shop.Cart.gift)}hideInfoDiscount();getDiscount()}function hideInfoDiscount(){var a=$(genObj.frameDiscount);a.empty();a.next(preloader).show()}function displayInfoDiscount(a){var b=$(genObj.frameDiscount);b.html(a);b.next(preloader).hide()}function applyGift(){$(genObj.gift).find(preloader).show();var a=0;$.ajax({url:"/mod_discount/gift/get_gift_certificate",data:"key="+$("[name=giftcert]").val(),type:"GET",success:function(b){if(b!=""){a=JSON.parse(b)}$(genObj.gift).find(preloader).hide();Shop.Cart.gift=a;recountCartPage()}});return false}function renderGiftInput(a){if(a==""){$(genObj.gift).empty()}else{$(genObj.gift).html(a)}}function giftError(a){$(genObj.gift).children(genObj.msgF).remove();if(a){$(genObj.gift).append(message.error(a));drawIcons($(genObj.gift).find(selIcons))}}function renderGiftSucces(a,b){$(genObj.certPrice).html(b.value);$(genObj.certFrame).show();$(genObj.gift).children(genObj.msgF).remove();$(genObj.gift).html(a)}function initOrder(){if(selectDeliv){cuselInit($(genObj.frameDelivery),methodDeliv);$(methodDeliv).on("change.methoddeliv",function(){var a=$(genObj.frameDelivery).find("span.cuselActive").attr("val");changeDeliveryMethod(a);recountCartPage()})}else{$(".check-variant-delivery").nStRadio({wrapper:$(".frame-radio > .frame-label"),elCheckWrap:".niceRadio",before:function(a){$(document).trigger("showActivity");$('[name="'+$(a).find("input").attr("name")+'"]').attr("disabled","disabled")},after:function(b,c){if(!c){var a=b.find("input").val();changeDeliveryMethod(a);recountCartPage();$('[name="'+$(b).find("input").attr("name")+'"]').removeAttr("disabled")}}})}if(selectPayment){cuselInit($(genObj.pM),"#paymentMethod")}else{$(genObj.pM).nStRadio({wrapper:$(".frame-radio > .frame-label"),elCheckWrap:".niceRadio"})}$(document).on("render_popup_cart",function(){recountCartPage()});$(document).on("sync_cart",function(){renderOrderDetails()});$(document).on("count_changed",function(){recountCartPage()});$(document).on("cart_rm",function(a){recountCartPage()});$(document).on("discount.display",function(a){displayInfoDiscount(a.tpl)});renderOrderDetails()}function initOrderTrEv(){$(document).on("discount.renderGiftInput",function(a){renderGiftInput(a.tpl)});$(document).on("discount.giftError",function(a){giftError(a.datas)});$(document).on("discount.renderGiftSucces",function(a){renderGiftSucces(a.tpl,a.datas)});$(document).on("displayDiscount",function(a){displayOrderSum(a.obj)})}$(document).on("scriptDefer",function(){initOrder()});