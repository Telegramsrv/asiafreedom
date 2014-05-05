/*
 * XenForo inline_mod.min.js
 * Copyright 2010-2014 XenForo Ltd.
 * Released under the XenForo License Agreement: http://xenforo.com/license-agreement
 */
(function(c,h,i){XenForo.InlineModForm=function(a){this.__construct(a)};XenForo.InlineModForm.prototype={__construct:function(a){a.is("form")&&a[0].reset();this.$form=a;this.$form.data("InlineModForm",this);this.$checkAll=this.$form.find("#ModerationCheck").click(c.context(this,"checkAll"));var b=c(this.$form.data("controls")),d=b.closest(".InlineMod");b.find(".SelectionCount").click(c.context(this,"nonCheckboxShowOverlay"));var e=c(".SelectionCountContainer");b.show().appendTo("body").wrap('<form id="InlineModOverlay" />');
e.length&&b.find(".SelectionCount").clone(!0).appendTo(e).attr("href","#").addClass("cloned");this.$totals=c(".InlineModCheckedTotal");d.hasClass("Hide")&&d.remove();this.overlay=null;this.cookieName=!1;this.selectedIds=[];this.overlay=this.lastCheck=null;if(a.data("cookiename"))this.cookieName="inlinemod_"+a.data("cookiename"),this.updateSelectedIdsFromCookie();c(i).one("XenForoActivationComplete",c.context(this,"setState"))},updateSelectedIdsFromCookie:function(){if(!this.cookieName)return[];var a=
this.selectedIds;this.selectedIds=(this.cookieValue=c.getCookie(this.cookieName))?this.cookieValue.split(","):[];return{original:a,selected:this.selectedIds,isDifferent:a!==this.selectedIds}},setState:function(){var a=this.selectedIds.length;this.$totals.closest(".cloned")[a?"addClass":"removeClass"]("itemsChecked");this.$totals.text(a)},recalculateSelected:function(){var a=this.selectedIds;this.$form.find("input:checkbox.InlineModCheck").each(function(){var b=c(this),d=b.data("XenForo.InlineModItem"),
e=b.prop("checked");d&&(a&&c.inArray(b.val(),a)>=0?e||(b.prop("checked",!0),d.setStyle()):e&&(b.prop("checked",!1),d.setStyle()))})},setSelectedIdState:function(a,b){this.updateSelectedIdsFromCookie().isDifferent&&setTimeout(c.context(this,"recalculateSelected"),0);var d=c.inArray(a,this.selectedIds);if(b){if(d>=0)return;this.selectedIds.push(a);this.selectedIds.sort(function(a,b){return a-b})}else{if(d<0)return;this.selectedIds.splice(d,1)}this.setState();this.cookieName&&(this.selectedIds.length?
c.setCookie(this.cookieName,this.selectedIds.join(",")):c.deleteCookie(this.cookieName))},checkAll:function(a){this.$form.find("input:checkbox.InlineModCheck").prop("checked",a.target.checked).trigger("change");a.target.checked||this.overlay.close();a.stopImmediatePropagation()},clickNextPrev:function(a){var b=this.$form.find("input:checkbox.InlineModCheck"),d=this.lastCheck,e=null;b.each(function(b,f){if(f==d)return e=c(a.target).hasClass("ClickPrev")?b-1:b+1,!1});if(e===null||e>=b.length)e=0;b.eq(e).get(0).focus();
c(this.lastCheck).length&&c(XenForo.getPageScrollTagName()).animate({scrollTop:b.eq(e).offset().top-c(this.lastCheck).offset().top+c(XenForo.getPageScrollTagName()).scrollTop()},XenForo.speed.normal);this.positionOverlay(b.eq(e).get(0))},createOverlay:function(){var a=c("#InlineModOverlay"),b=a.find("#ModerationSelect");a.children().show();a.children("#InlineModControls").css("display","block");a.overlay({closeOnClick:!1,fixed:!1,close:".OverlayCloser"});b.change(c.context(this,"chooseAction"));a.find("input:submit").click(c.context(this,
"chooseAction"));a.find(".ClickNext, .ClickPrev").click(c.context(this,"clickNextPrev"));return this.overlay=a.data("overlay")},positionOverlay:function(a){if(a.checked||this.$form.find("input:checkbox:checked.InlineModCheck").length){console.info("Position overlay next to %o",a);this.overlay||this.createOverlay();var b=this.overlay,d=c(a).offset(),e=d.left,d=d.top-15;XenForo.isRTL()?e-=b.getOverlay().outerWidth()+5:e+=17;if(b.getOverlay().outerWidth()+e>c(h).width()||e<0)e=0,d+=34;b.isOpened()?b.getOverlay().animate({left:e,
top:d},this.lastCheck&&!XenForo.isTouchBrowser()?XenForo.speed.normal:0,"easeOutBack"):(b.getConf().left=e-c(XenForo.getPageScrollTagName()).scrollLeft(),b.getConf().top=d-c(XenForo.getPageScrollTagName()).scrollTop(),b.load())}else this.overlay&&this.overlay.isOpened()&&this.overlay.close();this.lastCheck=a},nonCheckboxShowOverlay:function(a){a.preventDefault();if(!c(a.target).parents("#InlineModOverlay").length){this.overlay||this.createOverlay();this.overlay.load();var a=c(a.target).coords(),b=
this.overlay.getOverlay().coords(),b=XenForo.isRTL()?a.left:Math.max(0,a.left+a.width-b.width);this.overlay.getOverlay().css({left:b,top:a.top+a.height+5});delete this.lastCheck}},chooseAction:function(a){a.preventDefault();var b,d;b=c(a.target);b=b.is(":submit")&&b.attr("name")?b.attr("name"):c("#ModerationSelect").val();if(b=="")return this.overlay.close(),!1;if(this.running)return!1;d=b+"PreAction";if(typeof this[d]=="function")this[d](a);else this.execAction(b)},resetActionMenu:function(){this.overlay.getOverlay().get(0).reset()},
execAction:function(a){this.running=!0;XenForo.ajax(this.$form.attr("action"),this.$form.serialize()+"&a="+a,c.context(function(b,c){this.running=!1;this.resetActionMenu();if(XenForo.hasResponseError(b))return!1;if(XenForo.hasTemplateHtml(b)){var g={};g.title=b.title||b.h1;g.noCache=!0;new XenForo.ExtLoader(b,function(){console.info("show overlay");XenForo.createOverlay("",b.templateHtml,g).load()})}else if(b._redirectTarget){console.log("Inline moderation, %s complete.",a);var f=a+"PostAction";if(typeof this[f]==
"function")this[f](b,c);else XenForo.redirect(b._redirectTarget)}},this));var b=a+"MidAction";if(typeof this[b]=="function")this[b](a)},closeOverlayPreAction:function(){this.resetActionMenu();this.overlay.close()},deselectPreAction:function(){this.$form.find("input:checkbox.InlineModCheck:checked").prop("checked",!1).trigger("change");this.selectedIds=[];this.setState();this.cookieName&&c.deleteCookie(this.cookieName);this.$checkAll.prop("checked",!1);this.resetActionMenu();this.overlay.close()}};
XenForo.InlineModItem=function(a){this.__construct(a)};XenForo.InlineModItem.prototype={__construct:function(a){this.$form=a.closest("form");this.$ctrl=a.attr("title",XenForo.htmlspecialchars(a.attr("title")));XenForo.isTouchBrowser()||this.$ctrl.tooltip(XenForo.configureTooltipRtl({effect:"fade",fadeInSpeed:XenForo.speed.xfast,offset:[-10,-20],predelay:c.browser.msie?0:100,position:"top right",tipClass:"xenTooltip inlineModCheckTip",onBeforeShow:c.context(this,"beforeTooltip")}));this.arrowAppended=
!1;this.$target=c(a.data("target"));if(this.$form.data("InlineModForm")){var b=this.$form.data("InlineModForm");b.selectedIds.length&&c.inArray(a.val(),b.selectedIds)>=0&&a.prop("checked",!0)}this.$ctrl.bind({change:c.context(this,"setState"),click:c.context(this,"positionOverlay")});this.setStyle()},setState:function(){this.setStyle();var a=this.$form.data("InlineModForm");a&&a.setSelectedIdState(this.$ctrl.val(),this.$ctrl.prop("checked"))},setStyle:function(){this.$ctrl.is(":checked")?this.$target.addClass("InlineModChecked"):
this.$target.removeClass("InlineModChecked")},positionOverlay:function(a){if(this.$ctrl.data("target")){var b=this.$form.data("InlineModForm");b&&(this.$ctrl.data("tooltip")&&this.$ctrl.data("tooltip").hide(),b.positionOverlay(a.target))}},beforeTooltip:function(a){if(a.target.checked||this.$form.find("input:checkbox:checked.InlineModCheck").length)return!1;if(!this.arrowAppended)this.$ctrl.data("tooltip").getTip().append('<span class="arrow" />'),this.arrowAppended=!0}};XenForo.register("form.InlineModForm",
"XenForo.InlineModForm");XenForo.register("input:checkbox.InlineModCheck","XenForo.InlineModItem")})(jQuery,this,document);
