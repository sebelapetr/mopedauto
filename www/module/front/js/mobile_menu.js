function openSideNav(menuID, rightOrLeft = "right") {
    let menu = $("#" + menuID);
    let menuWidth = menu.outerWidth();
    let closeBtnMenu = $("#" + menuID + " .sidenav-close");

    if ($(window).width() > 660) {
        closeBtnMenu.css({"right": menuWidth + "px"});
    }
    menu.addClass("open").css(rightOrLeft, "0");
    $("#sidenav-overlay").css({"width": "100%", "opacity": "0.8"});
    $("body").addClass("lock-scroll");
}

function closeOverlay() {
    $("#sidenav-overlay").css({"width": "0%", "opacity": "0"});
}

function getAndCloseSideNav() {
    let openNav = $(".sidenav.open");

    openNav.each(function () {
        if ($(this).hasClass("sidenav-left")) {
            closeSideNav($(this).attr('id'), "left");
        } else {
            closeSideNav($(this).attr('id'));
        }
    });

    closeOverlay();
    $("body").removeClass("lock-scroll");
}

function closeSideNav(menuID, rightOrLeft = "right") {
    let menu = $("#" + menuID);
    let closeBtnMenu = $("#" + menuID + " .sidenav-close");
    let menuWidth = menu.outerWidth();
    /*console.log("menuWidth = " + menuWidth);*/
    let menuCloseBtnWidth = 0;
    if (closeBtnMenu[0] != null) {
        menuCloseBtnWidth = closeBtnMenu.outerWidth();
    }
    /*console.log("menuCloseWidth  = " + menuCloseBtnWidth);*/

    menu.removeClass("open").css(rightOrLeft, "-" + (menuWidth + menuCloseBtnWidth) + "px");
    closeBtnMenu.css(rightOrLeft, "-" + menuCloseBtnWidth + "px");
}