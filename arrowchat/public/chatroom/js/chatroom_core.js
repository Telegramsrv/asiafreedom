(function (a) {
    function oa(r, ea) {
        r = typeof r === "string" ? document.getElementById(r) : r;
        var O = r.cloneNode(false);
        O.innerHTML = ea;
        r.parentNode.replaceChild(O, r);
        return O
    }
	a.arrowchat = function () {
		function addHover($elements, classes) {
			$elements.each( function (i, element) {
				a(element).hover(
					function () {
						a(this).addClass(classes);
					}, function () {
						a(this).removeClass(classes);
					}
				);
			});
		}
		function displayMessage(id, message, type) {
			clearTimeout(message_timeout);
			if (a("#" + id).is(":visible")) {
				a("#" + id).hide(function() {
					a("#" + id + " .arrowchat_message_text").html(message);
					type == "error" && a(".arrowchat_message_box").css("background-color", "#ffe2e2").css("border-bottom", "1px solid #ffbebe");
					type == "notice" && a(".arrowchat_message_box").css("background-color", "#fffae2").css("border-bottom", "1px solid #ffecbe");
					a("#" + id).show();
				});
			} else {
				type == "error" && a(".arrowchat_message_box").css("background-color", "#ffe2e2").css("border-bottom", "1px solid #ffbebe");
				type == "notice" && a(".arrowchat_message_box").css("background-color", "#fffae2").css("border-bottom", "1px solid #ffecbe");
				a("#" + id + " .arrowchat_message_text").html(message);
				a("#" + id).show();
			}
			message_timeout = setTimeout(function () {
				a("#" + id).hide();
			}, 5000);
		}
		function chatroomUserOptions(data, is_admin) {
			a("#arrowchat_chatroom_make_mod_" + data.id).click(function () {
				a.post(c_ac_path + "includes/json/send/send_settings.php", {
					chatroom_mod: data.id,
					chatroom_id: Ccr
				}, function () {});
			});
			a("#arrowchat_chatroom_remove_mod_" + data.id).click(function () {
				a.post(c_ac_path + "includes/json/send/send_settings.php", {
					chatroom_remove_mod: data.id,
					chatroom_id: Ccr
				}, function () {});
			});
			a("#arrowchat_chatroom_block_user_" + data.id).click(function () {
				a.post(c_ac_path + "includes/json/send/send_settings.php", {
					block_chat: data.id
				}, function (json_data) {
					if (json_data != "-1") {
						if (typeof(blockList[data.id]) == "undefined") {
							blockList[data.id] = data.id;
						}
						displayMessage("arrowchat_chatroom_message_flyout", lang[103], "error");
					}
				})
			});
			a("#arrowchat_chatroom_ban_user_" + data.id).click(function () {
				var ban_length = prompt(lang[57]);
				if (ban_length != null && ban_length != "" && !(isNaN(ban_length))) {
					a.post(c_ac_path + "includes/json/send/send_settings.php", {
						chatroom_ban: data.id,
						chatroom_id: Ccr,
						chatroom_ban_length: ban_length
					}, function () {});
				}
			});
			a("#arrowchat_chatroom_visit_profile_" + data.id).click(function () {
				window.open(data.l);
			});
			a("#arrowchat_chatroom_private_message_" + data.id).click(function () {
				if (data.b != 1 || is_admin == 1) {
					if (u_id != data.id) {
						jqac.arrowchat.chatWith(data.id)
					}
				} else {
					displayMessage("arrowchat_chatroom_message_flyout", lang[46], "error");
				}
			});
			a("#arrowchat_chatroom_user_" + data.id).click(function () {
				if (crou != data.id) {
					a("#arrowchat_chatroom_user_" + crou).removeClass("arrowchat_chatroom_clicked");
					a("#arrowchat_chatroom_users_flyout_" + crou).removeClass("arrowchat_chatroom_create_flyout_display");
				}
				crou = data.id;
				a(this).toggleClass("arrowchat_chatroom_clicked");
				a("#arrowchat_chatroom_users_flyout_" + data.id).toggleClass("arrowchat_chatroom_create_flyout_display");
			});
		}
		function chatroomKeydown(key, $element) {
			if (key.keyCode == 13 && key.shiftKey == 0) {
				if (msgcount == 0) {
					setTimeout(function () {
						msgcount = 0;
					}, 15000);
				}
				msgcount++;
				if (msgcount > 4 && chatroom_admin != 1 && chatroom_mod != 1) {
					displayMessage("arrowchat_chatroom_message_flyout", lang[51], "error");
				} else {
					var i = $element.val();
					i = i.replace(/^\s+|\s+$/g, "");
					$element.val("");
					$element.focus();
					i != "" && a.ajax({
						url: c_ac_path + "includes/json/send/send_message_chatroom.php",
						type: "post",
						cache: false,
						dataType: "json",
						data: {
							userid: u_id,
							username: u_name,
							chatroomid: Ccr,
							message: i
						},
						beforeSend: function () {
							a(".arrowchat_popout_convo_input").addClass("arrowchat_message_sending_popout");
						},
						error: function () {
							a(".arrowchat_popout_convo_input").removeClass("arrowchat_message_sending_popout");
							displayMessage("arrowchat_chatroom_message_flyout", lang[135], "error");
						},
						success: function (e) {
							a(".arrowchat_popout_convo_input").removeClass("arrowchat_message_sending_popout");
							if (e) {
							addMessageToChatroom(e, u_name, i);
							a(".arrowchat_popout_convo").scrollTop(6E4);
						}
						}
					});
					return false
				}
			}
		}
		function addMessageToChatroom(b, c, d) {
			var title = "";
			if (chatroom_mod == 1)
				title = lang[137];
			if (chatroom_admin == 1)
				title = lang[136];
			d = d.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>").replace(/\"/g, "&quot;");
			d = replaceURLWithHTMLLinks(d);
			d = smileyreplace(d);
			if (a("#arrowchat_chatroom_message_" + b).length > 0) a("#arrowchat_chatroom_message_" + b + " .arrowchat_chatroom_message_content").html(formatTimestamp(new Date(Math.floor((new Date).getTime() / 1E3) * 1E3)) + d);
			else {
				a(".arrowchat_popout_convo").append('<div class="arrowchat_chatroom_box_message" id="arrowchat_chatroom_message_' + b + '"><img class="arrowchat_chatroom_message_avatar" src="'+u_avatar+'" alt="" /><div class="arrowchat_chatroom_message_name">' + c + title + ':</div><div class="arrowchat_chatroom_message_content  arrowchat_self">' + formatTimestamp(new Date(Math.floor((new Date).getTime() / 1E3) * 1E3)) + d + "</div></div>");
				a(".arrowchat_popout_convo").scrollTop(5E4)
			}
			showChatroomTime();
		}
        function receiveChatroom(c) {
			clearTimeout(Crref2);
			var global_mod = 0,
				global_admin = 0,
				admin_markup = "";
			chatroom_mod = 0;
			chatroom_admin = 0;
            a.ajax({
                url: c_ac_path + "includes/json/receive/receive_chatroom.php?popoutroom=1",
                cache: false,
                type: "post",
				data: {
					chatroomid: c
				},
                dataType: "json",
				success: function (b) {
					if (b) {
						var no_error = true;
						b && a.each(b, function (i, e) {
							if (i == "error") {
								a.each(e, function (l, f) {
									no_error = false;									
									Ccr = 0;
									chatroomreceived = 0;
									displayMessage("arrowchat_chatroom_message_flyout", f.m, "error");
								})
							}
						});
						if (no_error) {
							b && a.each(b, function (i, e) {
								if (i == "user_title") {
									a.each(e, function (l, f) {
										if (f.admin == 1) {
											global_admin = 1;
											chatroom_admin = 1;
										}
										if (f.mod == 1) {
											global_mod = 1;
											chatroom_mod = 1;
										}
									})
								}
								if (i == "chat_users") {
									var longname;
									a("#arrowchat_popout_friends").html("");
									a.each(e, function (l, f) {
										if ((global_admin == 1 || global_mod == 1) && (f.t == 1)) {
											admin_markup = '<div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_make_mod_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[52] + '</div></div><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_ban_user_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[53] + '</div></div>';
										}
										longname = renderHTMLString(f.n);
										f.n = renderHTMLString(f.n).length > 16 ? renderHTMLString(f.n).substr(0, 16) + "..." : f.n;
										a("<div/>").attr("id", "arrowchat_chatroom_user_" + f.id).css("position", "relative").mouseover(function () {
											a(this).addClass("arrowchat_chatroom_list_hover");
										}).mouseout(function () {
											a(this).removeClass("arrowchat_chatroom_list_hover");
										}).addClass("arrowchat_chatroom_room_list").addClass('arrowchat_chatroom_admin_' + f.t).html('<img class="arrowchat_chatroom_avatar" src="' + f.a + '"/><span class="arrowchat_chatroom_room_name">' + f.n + '</span><span class="arrowchat_userscontentdot arrowchat_' + f.status + '"></span>').appendTo("#arrowchat_popout_friends");
										a("<div/>").attr("id", "arrowchat_chatroom_users_flyout_" + f.id).css("right", "0px").css("top", "28px").addClass("arrowchat_chatroom_users_flyout").html('<div class="arrowchat_chatroom_title_padding"><div id="arrowchat_chatroom_title_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + longname + '<br/>' + lang[43] + '</div></div><hr class="arrowchat_options_divider"/><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_visit_profile_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[42] + '</div></div><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_block_user_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[84] + '</div></div>' + admin_markup).appendTo(a("#arrowchat_chatroom_user_" + f.id));
										if (f.t == 2) {
											a("#arrowchat_chatroom_title_" + f.id).html(longname + '<br/>' + lang[44])
										} else if (f.t == 3) {
											a("#arrowchat_chatroom_title_" + f.id).html(longname + '<br/>' + lang[45])
										}
										addHover(a(".arrowchat_chatroom_options_padding"), "arrowchat_options_padding_hover");
										chatroomUserOptions(f, global_admin);
										uc_avatar[f.id] = f.a;
									});
									a(".arrowchat_chatroom_admin_3").css("background-color", "#"+c_admin_bg);
									a(".arrowchat_chatroom_admin_3").css("color", "#"+c_admin_txt);
								}
							});
							if (c_disable_avatars == 1 || a("#arrowchat_setting_names_only :input").is(":checked")) {
								a(".arrowchat_chatroom_avatar").addClass("arrowchat_hide_avatars");
								a(".arrowchat_chatroom_message_avatar").addClass("arrowchat_hide_avatars");
							}
						}
					}
				}
            });
			Crref2 = setTimeout(function () {
				receiveChatroom(c)
			}, 6E4)
        }
		function loadChatroom(b, c, pass) {
			var global_mod = 0,
				global_admin = 0,
				admin_markup = "";
			chatroom_mod = 0;
			chatroom_admin = 0;
			chatroomreceived = 1;
			a.ajax({
				url: c_ac_path + "includes/json/receive/receive_chatroom_room.php",
				data: {
					chatroomid: b,
					chatroom_window: u_chatroom_open,
					chatroom_stay: u_chatroom_stay,
					chatroom_pw: pass
				},
				type: "post",
				cache: false,
				dataType: "json",
				success: function (o) {
					if (o) {
						clearTimeout(Crref2);
						var no_error = true;
						o && a.each(o, function (i, e) {
							if (i == "error") {
								a.each(e, function (l, f) {
									no_error = false;
									Ccr = 0;
									chatroomreceived = 0;
									displayMessage("arrowchat_chatroom_message_flyout", f.m, "error");
								});
							}
						});
						if (no_error) {
							setTimeout(function () {
								receiveChatroom(b)
							}, 30000);
							a(".arrowchat_popout_convo_input").keydown(function (h) {
								return chatroomKeydown(h, a(this))
							});
							o && a.each(o, function (i, e) {
								if (i == "user_title") {
									a.each(e, function (l, f) {
										if (f.admin == 1) {
											global_admin = 1;
											chatroom_admin = 1;
										}
										if (f.mod == 1) {
											global_mod = 1;
											chatroom_mod = 1;
										}
									});
								}
								if (i == "chat_name") {
									a.each(e, function (l, f) {										
										if (typeof crt2[b] == "undefined") {
											crt2[b] = f.n;
											document.title = crt2[b];
										}
									});
								}
								if (i == "chat_users") {
									var longname;
									a.each(e, function (l, f) {
										if ((global_admin == 1 || global_mod == 1) && (f.t == 1)) {
											admin_markup = '<div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_make_mod_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[52] + '</div></div><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_ban_user_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[53] + '</div></div>';
										}
										if (global_admin == 1 && f.t == 2) {
											admin_markup = '<div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_remove_mod_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[54] + '</div></div>';
										}
										longname = renderHTMLString(f.n);
										f.n = renderHTMLString(f.n).length > 16 ? renderHTMLString(f.n).substr(0, 16) + "..." : f.n;
										a("<div/>").attr("id", "arrowchat_chatroom_user_" + f.id).css("position", "relative").mouseover(function () {
											a(this).addClass("arrowchat_chatroom_list_hover");
										}).mouseout(function () {
											a(this).removeClass("arrowchat_chatroom_list_hover");
										}).addClass("arrowchat_chatroom_room_list").addClass('arrowchat_chatroom_admin_' + f.t).html('<img class="arrowchat_chatroom_avatar" src="' + f.a + '"/><span class="arrowchat_chatroom_room_name">' + f.n + '</span><span class="arrowchat_userscontentdot arrowchat_' + f.status + '"></span>').appendTo("#arrowchat_popout_friends");
										a("<div/>").attr("id", "arrowchat_chatroom_users_flyout_" + f.id).css("right", "0px").css("top", "28px").addClass("arrowchat_chatroom_users_flyout").html('<div class="arrowchat_chatroom_title_padding"><div id="arrowchat_chatroom_title_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + longname + '<br/>' + lang[43] + '</div></div><hr class="arrowchat_options_divider"/><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_visit_profile_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[42] + '</div></div><div class="arrowchat_chatroom_options_padding"><div id="arrowchat_chatroom_block_user_' + f.id + '" class="arrowchat_chatroom_flyout_text">' + lang[84] + '</div></div>' + admin_markup).appendTo(a("#arrowchat_chatroom_user_" + f.id));
										if (f.t == 2) {
											a("#arrowchat_chatroom_title_" + f.id).html(longname + '<br/>' + lang[44]);
										} else if (f.t == 3) {
											a("#arrowchat_chatroom_title_" + f.id).html(longname + '<br/>' + lang[45]);
										}
										addHover(a(".arrowchat_chatroom_options_padding"), "arrowchat_options_padding_hover");
										chatroomUserOptions(f, global_admin);
									});
									a(".arrowchat_chatroom_admin_3").css("background-color", "#"+c_admin_bg);
									a(".arrowchat_chatroom_admin_3").css("color", "#"+c_admin_txt);
								}
								if (i == "chat_history") {
									d = "";
									a.each(e, function (l, f) {
										if (typeof(blockList[f.userid]) == "undefined") {
											var title = "";
											if (f.mod == 1)
												title = lang[137];
											if (f.admin == 1)
												title = lang[136];
											l = "";
											fromname = f.n;
											if (f.n == u_name) {
												l = " arrowchat_self";
											}
											var sent_time = new Date(f.t * 1E3);
											if (f.global == 1) {
												d += '<div class="arrowchat_chatroom_box_message" id="arrowchat_chatroom_message_' + f.id + '"><div class="arrowchat_chatroom_message_content' + l + ' arrowchat_global_chatroom_message">' + formatTimestamp(sent_time) + f.m + "</div></div>"
											} else {
												d += '<div class="arrowchat_chatroom_box_message" id="arrowchat_chatroom_message_' + f.id + '"><img class="arrowchat_chatroom_message_avatar" src="'+f.a+'" alt="" /><div class="arrowchat_chatroom_message_name">' + fromname + title + ':</div><div class="arrowchat_chatroom_message_content' + l + '">' + formatTimestamp(sent_time) + f.m + "</div></div>"
											}
										}
									});
									a(".arrowchat_popout_convo").html("<div>" + d + "</div>");
									showChatroomTime();
									a(".arrowchat_popout_convo").scrollTop(5E4)
								}
							});
							if (c_disable_avatars == 1 || a("#arrowchat_setting_names_only :input").is(":checked")) {
								a(".arrowchat_chatroom_avatar").addClass("arrowchat_hide_avatars");
								a(".arrowchat_chatroom_message_avatar").addClass("arrowchat_hide_avatars");
							}
							a(".arrowchat_chatroom_message_input").focus();
						} else {
							if (c_user_chatrooms == "1") {
								$chatroom_create.show();
							}
						}
					}
				}
			})
		}
		function stripslashes(str) {
			str=str.replace(/\\'/g,'\'');
			str=str.replace(/\\"/g,'"');
			str=str.replace(/\\0/g,'\0');
			str=str.replace(/\\\\/g,'\\');
			return str;
		}
		function formatTimestamp(b) {
			var c = "am",
				d = b.getHours(),
				i = b.getMinutes(),
				e = b.getDate();
			b = b.getMonth();
			var g = d;
			if (d > 11) c = "pm";
			if (d > 12) d -= 12;
			if (d == 0) d = 12;
			if (d < 10) d = d;
			if (i < 10) i = "0" + i;
			var l = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
				f = "th";
			if (e == 1 || e == 21 || e == 31) f = "st";
			else if (e == 2 || e == 22) f = "nd";
			else if (e == 3 || e == 23) f = "rd";
			if (c_us_time != 1) {
				return e != Na ? '<span class="arrowchat_ts">' + g + ":" + i + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + g + ":" + i + "</span>"
			} else {
				return e != Na ? '<span class="arrowchat_ts">' + d + ":" + i + c + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + d + ":" + i + c + "</span>"
			}
		}
        function DTitChange(name) {
            if (dtit2 != 2) {
                document.title = lang[30] + " " + name + "!";
                dtit2 = 2
            } else {
                document.title = dtit;
                dtit2 = 1
            }
            if (window_focus == false) {
                dtit3 = setTimeout(function () {
                    DTitChange(name)
                }, 1000)
            } else {
                document.title = dtit;
                clearTimeout(dtit3);
            }
        }
        function replaceURLWithHTMLLinks(text) {
            var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
            return text.replace(exp, "<a target='_blank' href='$1'>$1</a>");
        }
        function smileyreplace(mess) {
            for (i = 0; i < Smiley.length; i++) {
                check_emoticon = mess.lastIndexOf(Smiley[i][1]);
                if (check_emoticon != -1) {
                    mess = mess.replace(Smiley[i][1], '<img class="arrowchat_smiley" height="16" width="16" src="' + c_ac_path + "themes/" + u_theme + '/images/smilies/' + Smiley[i][0] + '.gif" alt="" />');
                }
            }
            return mess;
        }
        function sa(b, c, d) {
            if (uc_name[b] == null || uc_name[b] == "") setTimeout(function () {
                sa(b, c, d)
            }, 500);
            else {
                Oa(b);
                if (d == 1) if (a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").length > 0) c = parseInt(a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").html()) + parseInt(c);
                if (c == 0) {
                    a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").remove();
                } else {
                    if (a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").length > 0) {
                        a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").html(c);
                    } else a("<div/>").addClass("arrowchat_popout_alert").html(c).appendTo(a("#arrowchat_popout_user_" + b + " .arrowchat_popout_wrap"));
                }
                y[b] = c;
                S();
            }
        }
        function S() {
            var b = "",
                c = 0;
            for (chatbox in y) if (y.hasOwnProperty(chatbox)) if (y[chatbox] != null) {
                b += chatbox + "|" + y[chatbox] + ",";
                if (y[chatbox] > 0) c = 1
            }
            Ka = c;
            b.slice(0, -1)
        }
		function M() {
			a(".arrowchat_popout_convo").css("height", a(window).height() - a(".arrowchat_popout_input_container").height() - 20);
		}
        function playNewMessageSound() {
            swfobject.embedSWF(c_ac_path + "themes/" + u_theme + "/sounds/new%5Fmessage.player.swf?soundswf="+c_ac_path+"themes/" + u_theme + "/sounds/new%5Fmessage.swf&autoplay=1&loops=0", "arrowchat_sound_player_holder", "1", "1", "9.0.0");
        }
        function ha(b) {
            var c = "am",
                d = b.getHours(),
                i = b.getMinutes(),
                e = b.getDate();
            b = b.getMonth();
            if (d > 11) c = "pm";
            if (d > 12) d -= 12;
            if (d == 0) d = 12;
            if (d < 10) d = d;
            if (i < 10) i = "0" + i;
            var l = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                f = "th";
            if (e == 1 || e == 21 || e == 31) f = "st";
            else if (e == 2 || e == 22) f = "nd";
            else if (e == 3 || e == 23) f = "rd";
			if (c_us_time != 1) {
				return e != Na ? '<span class="arrowchat_ts">' + g + ":" + i + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + g + ":" + i + "</span>"
			} else {
				return e != Na ? '<span class="arrowchat_ts">' + d + ":" + i + c + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + d + ":" + i + c + "</span>"
			}
        }
        function Ca(b, c, d) {
            a("#arrowchat_popout_user_" + d + "_popup .arrowchat_popout_convo").scrollTop(a("#arrowchat_popout_user_" + d + "_convo .arrowchat_popout_convo")[0].scrollHeight)
        }
		function pushSubscribe() {
			if (c_push_engine == 1) {
				push.subscribe({ "channel" : "chatroom"+ac_chatroom_id, "callback" : function(data) { pushReceive(data); } });
			}
		}
		function addChatroomMessage(id, name, message, userid, sent, global, mod, admin) {
			if (userid == u_id) {
				uc_avatar[u_id] = u_avatar;
			}
			message = stripslashes(message);
			message = replaceURLWithHTMLLinks(message);
			var sent_time = new Date(sent * 1E3);
			if (typeof(uc_avatar[u_id]) == "undefined") {
				a.ajax({
					url: c_ac_path + "includes/json/receive/receive_user.php",
					data: {
						userid: userid
					},
					type: "post",
					cache: false,
					dataType: "json",
					success: function (data) {
						if (data) {
							uc_avatar[userid] = data.a;
							chatroomDiv(id, uc_avatar[userid], name, sent_time, message, global, mod, admin);
						}
					}
				});
			} else {
				chatroomDiv(id, uc_avatar[userid], name, sent_time, message, global, mod, admin);
			}
			count++;	
		}
		function chatroomDiv(id, image, name, time, message, global, mod, admin) {
			var title = "";
			if (mod == 1)
				title = lang[137];
			if (admin == 1)
				title = lang[136];
			if (a("#arrowchat_chatroom_message_" + id).length > 0) {
			} else {
				if (global == 1) {
					a("<div/>").attr("id", "arrowchat_chatroom_message_" + id).addClass("arrowchat_chatroom_box_message").html('<div class="arrowchat_chatroom_message_content arrowchat_global_chatroom_message">' + formatTimestamp(time) + message + "</div>").appendTo(a(".arrowchat_popout_convo"));
				} else {
					a("<div/>").attr("id", "arrowchat_chatroom_message_" + id).addClass("arrowchat_chatroom_box_message").html('<img class="arrowchat_chatroom_message_avatar" src="'+image+'" alt="" /><div class="arrowchat_chatroom_message_name">' + name + title + ':</div><div class="arrowchat_chatroom_message_content">' + formatTimestamp(time) + message + "</div>").appendTo(a(".arrowchat_popout_convo"));
				}
			}
			if (c_disable_avatars == 1) {
				a(".arrowchat_chatroom_message_avatar").addClass("arrowchat_hide_avatars");
			}
			a(".arrowchat_popout_convo").scrollTop(6E4);
			showChatroomTime();
		}
		function showChatroomTime() {
			a(".arrowchat_chatroom_box_message").mouseenter(function () {
				a(this).children(".arrowchat_chatroom_message_content").children(".arrowchat_ts").show();
			});
			a(".arrowchat_chatroom_box_message").mouseleave(function () {
				a(this).children(".arrowchat_chatroom_message_content").children(".arrowchat_ts").hide();
			});
		}
		function pushReceive(data) {
			if ("chatroommessage" in data) {
				if (typeof(blockList[data.chatroommessage.userid]) == "undefined")
				{
					addChatroomMessage(data.chatroommessage.id, data.chatroommessage.name, data.chatroommessage.message, data.chatroommessage.userid, data.chatroommessage.sent, data.chatroommessage.global, data.chatroommessage.mod, data.chatroommessage.admin);
					if (data.chatroommessage.userid != u_id) {
						u_chatroom_sound == 1 && !a(".arrowchat_popout_convo_input").is(":focus") && playNewMessageSound();
					}
				}
			}
		}
		function cancelJSONP() {
			if (typeof CHA != "undefined") {
				clearTimeout(CHA);
			}
			if (typeof xOptions != "undefined") {
				xOptions.abort();
			}
		}
		function receiveCore() {
			cancelJSONP();
			var url = c_ac_path + "includes/json/receive/receive_core.php?hash=" + u_hash_id + "&init=" + acsi + "&room=" + Ccr;
			xOptions = a.ajax({
				url: url,
				dataType: "jsonp",
				success: function (b) {
					V.timestamp = ma;
					var c = "",
						d = {};
					d.available = "";
					d.busy = "";
					d.offline = "";
					d.away = "";
					if (b && b != null) {
						var i = 0;
						a.each(b, function (e, l) {
							if (e == "chatroom") {
								var d1 = 0,
									d2 = "";
								a.each(l, function (f, h) {
									if (typeof(blockList[h.userid]) == "undefined") {
										addChatroomMessage(h.id, h.n, h.m, h.userid, h.t, h.global, h.mod, h.admin);
									}
									d2 = h;
									d1++;
								});
								if (typeof d2 != "undefined") {
									if (typeof(blockList[d2.userid]) == "undefined") {
										showChatroomTime();
										if (d2.userid != u_id) {
											u_chatroom_sound == 1 && !a(".arrowchat_popout_convo_input").is(":focus") && playNewMessageSound();
										}
									}
								}
							}
						});
					}
					if ($ != 1 && w != 1) {
						K++;
						if (K > 4) {
							D *= 2;
							K = 1
						}
						if (D > 12E3) D = 12E3
					}
					acsi++;
				}
			});
			if (isAway == 1) {
				var CHT = c_heart_beat * 1000 * 3;
			} else {
				var CHT = c_heart_beat * 1000;
			}
			if (c_push_engine != 1) {
				CHA = setTimeout(function () {
					receiveCore()
				}, CHT);
			}
		}
		function renderHTMLString(string) {
			var render = a("<div/>").attr("id", "arrowchat_render").html(string).appendTo('body');
			var new_render = a("#arrowchat_render").html()
			render.remove();
			return new_render;
		}
        var bounce = 0,
            bounce2 = 0,
			chatroom_mod = 0,
			chatroom_admin = 0,
            count = 0,
            V = {},
			uc_avatar = {},
            dtit = document.title,
            dtit2 = 1,
            dtit3, window_focus = true,
            xa = {},
			message_timeout,
            j = "",
            crou = "",
            $ = 0,
            w = 0,
            bli = 1,
			isAway = 0,
            chatroomreceived = 0,
            msgcount = 0,
            W = false,
            Y, Z, E = 3E3,
            Crref2, Ccr = -1,
            D = E,
            K = 1,
            ma = 0,
            R = 0,
            m = "",
            Ka = 0,
            crt = {},
            crt2 = {},
            y = {},
            G = {},
            aa = {},
            ca = {},
            Aa = new Date,
            Na = Aa.getDate(),
            ab = Math.floor(Aa.getTime() / 1E3),
            acsi = 1,
            Q = 0,
            fa = -1,
            acp = "Powered By <a href='http://www.arrowchat.com/' target='_blank'>ArrowChat</a>",
            pa = 0,
            B, N;
        var _ts = "",
            _ts2;
        for (d = 0; d < Themes.length; d++) {
            if (Themes[d][2] == u_theme) {
                _ts2 = "selected";
            } else {
                _ts2 = "";
            }
            _ts = _ts + "<option value=\"" + Themes[d][0] + "\" " + _ts2 + ">" + Themes[d][1] + "</option>";
        }
        arguments.callee.videoWith = function (b) {
            var win = window.open(c_ac_path + 'video_chat.php?rid=' + b, 'audiovideochat', "status=no,toolbar=no,menubar=no,directories=no,resizable=no,location=no,scrollbars=no,width=650,height=610");
            win.focus();
        };
		function Xa() {
			if (u_id != "") {
				Ccr = ac_chatroom_id;
				if (c_push_engine == 1) {
					push = PUBNUB.init({
						publish_key   : c_push_publish,
						subscribe_key : c_push_subscribe
					});
				}
				if (c_push_engine == 1) {
					pushSubscribe();
				} else {
					receiveCore();
				}
				loadChatroom(ac_chatroom_id, "1");
				a("<div/>").attr("id", "arrowchat_popout_user_" + ac_chatroom_id + "_convo").addClass("arrowchat_popout_convo_wrapper").html('<div id="arrowchat_popout_text_' + ac_chatroom_id + '" class="arrowchat_popout_convo" style="padding-left:5px;"></div><div class="arrowchat_popout_input_container"><div class="arrowchat_popout_input_wrapper"><textarea class="arrowchat_popout_convo_input"></textarea></div></div>').appendTo(a("#arrowchat_popout_chat")).show();
				M();
				a(window).bind("resize", M);
				a(".arrowchat_popout_convo_input").focus();
			}
		}
        a.ajaxSetup({
            scriptCharset: "utf-8",
            cache: false
        });
        arguments.callee.runarrowchat = function () {
            Xa()
        };
    }
})(jqac);
(jqac);
jqac(document).ready(function () {
    jqac.arrowchat();
    jqac.arrowchat.runarrowchat()
});