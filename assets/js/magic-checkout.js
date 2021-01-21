function validateEmail(mail) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(mail);
}

function submitWait(active) {
    if (active) {
        jQuery("#submitMagic").attr("disabled", false);
        jQuery("#submitMagic").val("ACHETER");
    } else {
        jQuery("#submitMagic").attr("disabled", true);
        jQuery("#submitMagic").val("Chargement...");
    }
}

function updateTotalPrice() {
    submitWait(false);
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: "html",
        data: {
            "action": "getTotalPrice"
        }
    }).success(function (result) {
        submitWait(true);
        jQuery("#totalPrice").html(result);
    });
}

function showLoader(container) {
    var loader = "<div id='magicLoader'>" +
        "<img src='data:image/png;base64,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' alt='Logo'/> " +
        "<div class='loaderMagic'></div>" +
        "</div>";
    jQuery("#" + container).append(loader);
}

function removeLoader() {
    jQuery("#magicLoader").remove();
}


function magicClosePopup(elemBouton) {

    /* if(typeof elemBouton.data("idorder") !== "undefined"){
        console.log("reused");
        elemBouton.parent().parent().remove();
    }else {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            data: {
                "action": "clearCart"
            }
        }).complete(function(){
            elemBouton.parent().parent().remove();
        });
    }
     */
    elemBouton.parent().parent().remove();
}

function exitHtml(idOrder){
    var html = "<a class='closeIframe'><img src='/wp-content/plugins/woocommerce-magic-checkout/assets/images/close.svg' alt='X'/> </a>";
    if (typeof idOrder !== "undefined"){
        html = "<a class='closeIframe' data-idorder='"+idOrder+"'><img src='/wp-content/plugins/woocommerce-magic-checkout/assets/images/close.svg' alt='X'/> </a>";

    }
    return html;
}

jQuery(document).ready(function ($) {
    var requestEmailSent = false;
    var requestCodePromoSent = false;
    var requestCrossSell = false;
    var requestCrossSellAjax;

    $(".magic-button").on("click", function (e) {
        var idProduct = $(this).data("idproduct");
        var textvalidate = $(this).data("textvalidate");
        var textpayment = $(this).data("textpayment");
        var container = "<div id='magicPopupContainer'></div>";
        $("body").append(container);
        showLoader("magicPopupContainer");
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "html",
            data: {
                "action": "getPopupDisplay",
                "idproduct": idProduct,
                "textvalidate": textvalidate,
                "textpayment": textpayment
            }
        })
            .success(function (result) {
                removeLoader();
                $("#magicPopupContainer").append(result);
                $("#magic-checkout").append(exitHtml());
            });
    });
    $("body").on("click", "#magic-checkout-form #showCodePromo", function () {
        if ($("#magic-checkout-form #codepromo").hasClass("hide")) {
            $("#magic-checkout-form #codepromo").removeClass("hide");
            $("#magic-checkout-form #codepromo").addClass("show");
            $("#magic-checkout-form #submitPromo").removeClass("hide");
            $("#magic-checkout-form #submitPromo").addClass("show");
        } else if ($("#magic-checkout-form #codepromo").hasClass("show")) {
            $("#magic-checkout-form #codepromo").removeClass("show");
            $("#magic-checkout-form #codepromo").addClass("hide");
            $("#magic-checkout-form #submitPromo").removeClass("show");
            $("#magic-checkout-form #submitPromo").addClass("hide");
        } else {
            $("#magic-checkout-form #codepromo").removeClass("show");
            $("#magic-checkout-form #codepromo").removeClass("hide");
            $("#magic-checkout-form #codepromo").addClass("hide");
            $("#magic-checkout-form #submitPromo").removeClass("show");
            $("#magic-checkout-form #submitPromo").removeClass("hide");
            $("#magic-checkout-form #submitPromo").addClass("hide");
        }
    }).on("blur", "#magic-checkout-form #email", function () {
        if (validateEmail($(this).val())) {
            if (!requestEmailSent) {
                requestEmailSent = true;
                var email = $(this).val();
                submitWait(false);
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    dataType: "json",
                    data: {
                        "action": "emailExist",
                        "email": email
                    }
                }).complete(function (result) {
                    requestEmailSent = false;
                    if (result.responseJSON === true) {
                        $("#magic-checkout-form #password").removeClass("hide").addClass("show").prop("required", true);
                        $("#magic-checkout-form input[name='userKnow']").val("yes");
                    }else{
                        $("#magic-checkout-form #password").removeClass("show").addClass("hide").prop("required", false);
                        $("#magic-checkout-form input[name='userKnow']").val("no");
                    }
                    submitWait(true);
                });
            }
        } else {
            $("#magic-checkout-form #password").removeClass("show").addClass("hide").prop("required", false);
            $("#magic-checkout-form input[name='userKnow']").val("no");
            $("#magic-checkout-form #email").removeClass("error");
            $("#magic-checkout-form #password").removeClass("error");
            $("#magic-checkout-form .loginInfo .error").remove();
        }
    }).on("click", "#magic-checkout-form #submitPromo", function () {
        if (!requestCodePromoSent) {
            requestCodePromoSent = true;
            var addCoupon = "true";
            var codePromo = $("#codepromo").val();
            if (codePromo.length === 0) {
                addCoupon = "false";
            }
            submitWait(false);
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    "action": "couponApply",
                    "coupon": codePromo,
                    "addCoupon": addCoupon
                }
            }).complete(function (result) {
                requestCodePromoSent = false;
                if (result.responseJSON === true) {
                    $("#magic-checkout-form input[name='codePromoApply']").val("yes");
                    $("#magic-checkout-form #codepromo").removeClass("invalid");
                    $("#magic-checkout-form #submitPromo").removeClass("invalid");
                    $("#magic-checkout-form #codepromo").addClass("valid");
                    $("#magic-checkout-form #submitPromo").addClass("valid");
                } else {
                    $("#magic-checkout-form input[name='codePromoApply']").val("no");
                    $("#magic-checkout-form #codepromo").removeClass("valid");
                    $("#magic-checkout-form #submitPromo").removeClass("valid");
                    $("#magic-checkout-form #codepromo").addClass("invalid");
                    $("#magic-checkout-form #submitPromo").addClass("invalid");
                }
                submitWait(false);
                updateTotalPrice();

            });
        }
    }).on("change", "#magic-checkout-form #crosssell", function () {
        if (!requestCrossSell) {
            requestCrossSell = true;
        }else{
            if(requestCrossSellAjax && requestCrossSellAjax.readyState !== 4) {
                requestCrossSellAjax.abort();
            }
        }
            var crosssellID = $(this).val();
            var addItem = "true";
            if (!$(this).is(":checked")) {
                addItem = "false";
            }
            submitWait(false);
            requestCrossSellAjax  = $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    "action": "addCrossSell",
                    "idCross": crosssellID,
                    "addItem": addItem
                }
            }).success(function (result) {
                requestCrossSell = false;
                submitWait(true);
                updateTotalPrice();
            });
    }).on("submit", "#magic-checkout-form", function (e) {
        var container = "<div id='magicPaiementContainer'></div>";
        $("body").prepend(container);
        $("p.error").remove();
        showLoader("magicPaiementContainer");
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: ajaxurl + "?pay_for_order=true",
            dataType: "json",
            data: form.serialize()
        }).complete(function (result) {
            var resultats = result.responseJSON;
            if (resultats === 401) {
                removeLoader();
                $("#magicPaiementContainer").remove();
                if ($("input[name='userKnow']").val() === "yes") {
                    // Login Failed
                    $("#email").addClass("error");
                    $("#password").addClass("error").after("<p class='error'>Mot de passe incorrect. <a href='/mon-compte/mot-de-passe-oublie/'>Mot de passe perdu ? </a></p>");
                } else {
                    // Inscription Failed
                    $("#password").after("<p class='error'>Une erreur est survenue lors de l'inscription</p>");
                }
            } else {
                //$("#magicPaiementContainer").html(result.responseText);
                if($(".loginInfo").find("input").length !== 0) {
                    $(".loginInfo").html("<p class='emailConnected'>" + resultats.user + "</p>");
                }
                $("#magicPaiementContainer").append("<div style='display:none' id='magic-paiement' class='" + resultats.paiementMethod + "'><iframe data-textpayment='"+resultats.textpayment+"' name='paiementIframe' id='paiementIframe' src='/valider-commande/payer-la-commande/" + resultats.idOrder + "/?ismagic=true&pay_for_order=" + resultats.pay_for_order + "&key=" + resultats.key + "&paiementMethod=" + resultats.paiementMethod + "'></iframe></div>");
                $("#magic-paiement").append(exitHtml(resultats.idOrder));
            }
        });
    }).on("click", ".closeIframe", function () {
        magicClosePopup($(this));
    });
    $("#paiementIframe").livequery(function (e) {
        $("#magic-paiement").css({"display": "block"});
        removeLoader();
    });
});

