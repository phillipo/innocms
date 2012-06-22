//@todo: eliminare i riferimenti alla callback
//@todo: inserire il commento di intestazione
//@todo: cambiare nome!!
(function ($) {
    $.fn.jic = function (j) {
        var k = $.extend({
            input_type: 'radio',
            selection_mode: 'suffix',
            selection_callback: null,
            on_suffix: '_on',
            images: null,
            images_path: "images/",
            images_extension: "." + "png",
            overlay_elem: 'span',
            overlay_class: 'check',
            label_elem: 'label',
            label_class: 'label_jic',
            _css_prefix: '_jjic_',
            _checked: '_checked',
            _unchecked: '_unchecked'
        }, j || {});
        try {
            this.find(k.label_elem).css('display', 'inline-block').css('position', 'relative').css('cursor', 'pointer')
        } catch (e) {
            alert(e)
        }
        var l = this.find(k.label_elem + '.' + k.label_class + ' input:' + k.input_type);
        var m = null;
        switch (k.selection_mode) {
        case 'overlay':
            m = function (a) {
                var b = $("<" + k.overlay_elem + " class='" + k.overlay_class + "'></span>");
                if (a.prop('checked')) {
                    if (!a.parent().find(k.overlay_elem + '.' + k.overlay_class).attr('class')) {
                        a.parent().append(b.hide().fadeIn())
                    }
                } else {
                    a.parent().find(k.overlay_elem + '.' + k.overlay_class).remove()
                }
            };
            break;
        case 'suffix':
            m = function (a) {
                if (a.prop('checked')) {
                    a.parent().find("img." + k._css_prefix + k._checked).show();
                    a.parent().find("img." + k._css_prefix + k._unchecked).hide()
                } else {
                    a.parent().find("img." + k._css_prefix + k._checked).hide();
                    a.parent().find("img." + k._css_prefix + k._unchecked).show()
                }
            };
            break;
        case 'callback':
            m = k.selection_callback;
            break
        }
        m = k.selection_callback ? k.selection_callback : m;
        var n = function (a, b) {
                var c = a.prop('checked');
                var d = k.images_path + k.images[b] + k.on_suffix + k.images_extension;
                var e = k.images_path + k.images[b] + k.images_extension;
                var f = $("<img class='" + k._css_prefix + k._unchecked + "'>").attr('src', e);
                var g = $("<img class='" + k._css_prefix + k._checked + "'>").attr('src', d);
                var h = c ? g : f;
                var i = !c ? g : f;
                a.parent().append(h).append(i.css('display', 'none'))
            };
        var o = function (a, b) {
                var c = k.images_path + k.images[b] + k.images_extension;
                a.parent().append($("<img/>").attr('src', c));
                m(a)
            };
        var p = function (a, b) {
                console.log('_set_image_by_sprite ')
            };
        var q = function (a, b) {
                var c = null;
                switch (k.selection_mode) {
                case 'suffix':
                    n(a, b);
                    break;
                case 'callback':
                case 'overlay':
                    o(a, b);
                    break;
                case 'sprite':
                    p(a, b);
                    break
                }
            };
        var r = 0;
        var s = function (a) {
                q(a, r);
                a.css('left', '-9999px').css('position', 'absolute');
                ++r
            };
        l.each(function () {
            s($(this))
        });
        var t = $.browser.msie && $.browser.version < 9 ? true : false;
        var u = t ? this.find(k.label_elem + '.' + k.label_class) : l;
        if (t) {
            u.click(function () {
                var a = '#' + $(this).attr('for');
                var b = $(this).find('input:' + k.input_type + a);
                b.prop('checked', !b.prop('checked'));
                l.trigger('change')
            })
        }
        if (k.input_type == 'radio') {
            l.change(function () {
                l.each(function () {
                    m($(this))
                })
            })
        } else if (k.input_type == 'checkbox') {
            l.change(function () {
                m($(this))
            })
        }
        return this
    }
})(jQuery);