;(function ($, window, document, undefined) {

        let pluginName = 'tree',
            defaults = {
                id: '',
                jstree: {
                    plugins: ['core', 'contextmenu'],
                },     // jstreeOptions
                actions: {
                    create: {
                        url: '',
                        label: '',
                        icon: ''
                    },
                    rename: {
                        url: '',
                        label: '',
                        icon: ''
                    },
                    remove: {
                        url: '',
                        label: '',
                        icon: ''
                    },
                    select: {
                        url: ''
                    },
                    move: {
                        url: ''
                    }
                }
            };

        let id;
        let hash;
        let $this;
        let jstreeOptions;
        let selector;
        let csrfToken;
        let actions;

        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, defaults, options);
            this._defaults = defaults;
            this._name = pluginName;
            id = this.options.id;
            hash = $this.data('hash');
            jstreeOptions = this.options.jstree;
            selector = $("#" + hash);
            csrfToken = yii.getCsrfToken();
            actions = this.options.actions;
            this.init();
        }

        Plugin.prototype.init = function () {
            let contextmenu = {'contextmenu': {'items': menu},};
            let options = $.extend({}, contextmenu, jstreeOptions);
            InitTree(options);
        };

        this.InitTree = function (options) {
            let jtree = selector.jstree(options);

            /*jtree.bind("changed.jstree", function (e, data){
                console.log(data.selected[0]);
            });*/


            jtree.bind("copy_node.jstree", function (e, data) {
                alert("Copied `" + data.inst.get_text(data.rslt.original) + "` inside `" + (data.rslt.parent === -1 ? 'the main container' : data.inst.get_text(data.rslt.parent)) + "` at index " + data.rslt.position);
            });


            jtree.bind('create_node.jstree', function (event, data) {
                console.log(data);
                let xhr = new XMLHttpRequest();
                xhr.open('PUT', actions.create.url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                let _post_data = 'parent=' + data.node.parent;
                xhr.send(encodeURI(_post_data));

                xhr.onreadystatechange = function () {
                    if (this.readyState !== 4) {
                        return false;
                    }

                    if (this.status !== 200) {
                        throw Error('Error: ' + this.status);
                    }

                    let response = JSON.parse(this.responseText);
                    if (response.status === 'success') {
                        // OK
                        selector.jstree(true).set_id(data.node, response.id);
                        selector.jstree(true).edit(data.node);
                    } else {
                        console.log(response.status);
                        throw Error(response.message);
                    }
                };
            });

            jtree.bind('rename_node.jstree', function (event, data) {
                console.log(event.type + ' ID: ' + data.node.id);
                let xhr = new XMLHttpRequest();
                xhr.open('POST', actions.rename.url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                let _post_data = 'id=' + data.node.id + '&' + 'name=' + data.node.text;
                xhr.send(encodeURI(_post_data));

                xhr.onreadystatechange = function () {

                    if (this.readyState !== 4) {
                        return false;
                    }

                    if (this.status !== 200) {
                        throw Error('Error: ' + this.status);
                    }

                    let response = JSON.parse(this.responseText);
                    if (response.status !== 'success') {
                        console.log(response.status);
                        throw Error(response.message);
                    }
                };
            });

            jtree.bind('delete_node.jstree', function (event, data) {
                //console.log(event.type + ' ID: ' + data.node.id);

                let xhr = new XMLHttpRequest();
                xhr.open('POST', actions.remove.url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                let _post_data = 'id=' + data.node.id;
                xhr.send(encodeURI(_post_data));

                xhr.onreadystatechange = function () {
                    if (this.readyState !== 4) {
                        return false;
                    }

                    if (this.status !== 200) {
                        throw Error('Error: ' + this.status);
                    }

                    let response = JSON.parse(this.responseText);
                    if (response.status !== 'success') {
                        console.log("Status: " + response.status);
                        throw Error(response.message);
                    }
                };
            });

            if (typeof actions.move !== "undefined") {
                jtree.bind('move_node.jstree', function (event, data) {
                    $.post(actions.move.url, {
                            id: data.node.id,
                            prev_id: $('#' + data.node.id).prev().attr('id'),
                            parent_id: data.node.parent
                        },
                        function (data, textStatus) {
                            if (textStatus !== 'success') {
                                console.log("Status: " + data.status);
                                console.log(data.message);
                            }
                        });
                });
            }

            if (typeof actions.select !== "undefined") {
                jtree.bind('select_node.jstree', function (event, data) {
                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', actions.select.url + '?id=' + data.node.id, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    let _post_data = null;
                    xhr.send(encodeURI(_post_data));

                    xhr.onreadystatechange = function () {

                        if (this.readyState !== 4) {
                            return false;
                        }

                        if (this.status !== 200) {
                            throw Error('Error: ' + this.status);
                        }

                        $('#form').html(this.responseText);
                    };
                    //return data.instance.toggle_node(data.node);
                });
            }

            jtree.bind('open_node.jstree', function (event, data) {
                //data.instance.set_type(data.node,'f-open');
            });

            jtree.bind('close_node.jstree', function (event, data) {
                //data.instance.set_type(data.node,'f-closed');
            });
        };

        this.menu = function (node) {
            let t = selector.jstree(true);
            let  items = {};

            if (typeof actions.create !== "undefined") {
                items = $.extend({}, items, {
                    Create: {
                        label: actions.create.label,
                        icon: actions.create.icon,
                        action: function (obj) {
                            let selected = t.get_selected(false); // array
                            console.log(selected);
                            // TODO: complete node creation
                            //node = t.create_node(node);
                        }
                    }
                });
            }

            if (typeof actions.rename !== "undefined") {
                items = $.extend({}, items, {
                    Rename: {
                        label: actions.rename.label,
                        icon: actions.rename.icon,
                        action: function (obj) {
                            t.edit(node);
                        }
                    }
                });
            }

            if (typeof actions.remove !== "undefined") {
                items = $.extend({}, items, {
                    Remove: {
                        label: actions.remove.label,
                        icon: actions.remove.icon,
                        action: function (obj) {
                            t.delete_node(node);
                        }
                    }
                });
            }

            return items;
        };

        $.fn[pluginName] = function (options) {

            $this = $(this);

            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName,
                        new Plugin(this, options));
                }
            });
        };
    }
)(jQuery, window, document);