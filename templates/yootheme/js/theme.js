// Theme JavaScript
(function ($, UIkit) {

    UIkit.component('header', {

        name: 'header',

        connected: function () {
            this.initialize();
        },

        ready: function () {
            if (!this.section.length) {
                this.initialize();
            }
        },

        update: [

            {

                read: function () {
                    this.prevHeight = this.height;
                    this.height = this.$el[0].offsetHeight;
                    var sticky = this.modifier && UIkit.getComponent(this.sticky, 'sticky');
                    if (sticky) {
                        sticky.$props.top = this.section[0].offsetHeight <= window.innerHeight
                            ? this.selector
                            : UIkit.util.offsetTop(this.section) + 300;
                    }
                },

                write: function () {
                    if (this.placeholder && this.prevHeight !== this.height) {
                        this.placeholder.css({height: this.height});
                    }
                },

                events: ['load', 'resize']

            }

        ],

        methods: {

            initialize: function () {

                this.selector = '.tm-header + [class*="uk-section"], .tm-header + > [class*="uk-section"]';
                this.section = $(this.selector);
                this.sticky = $('[uk-sticky]', this.$el);
                this.modifier = this.section.attr('tm-header-transparent');

                if (!this.modifier || !this.section.length) {
                    return;
                }

                this.$el.addClass('tm-header-transparent');

                this.placeholder = this.section[0].hasAttribute('tm-header-transparent-placeholder')
                    && $('<div class="tm-header-placeholder uk-margin-remove-adjacent" style="height: ' + this.$el[0].offsetHeight + 'px"></div>').insertBefore($('[uk-grid]', this.section).first());

                var container = $('.uk-navbar-container', this.$el),
                    navbar = $('[uk-navbar]', this.$el),
                    cls = 'uk-navbar-transparent uk-' + this.modifier;

                $('.tm-headerbar-top, .tm-headerbar-bottom').addClass('uk-' + this.modifier);

                if (navbar.attr('dropbar-mode') === 'push') {
                    navbar.attr('dropbar-mode', 'slide');
                }

                if (!this.sticky.length) {
                    container.addClass(cls);
                } else {
                    this.sticky.attr({
                        animation: 'uk-animation-slide-top',
                        top: this.selector,
                        'cls-inactive': cls
                    });
                }
            }

        }

    });

    if (UIkit.util.isRtl) {

        var mixin = {

            init: function () {
                this.$props.pos = UIkit.util.swap(this.$props.pos, 'left', 'right');
            }

        };

        UIkit.mixin(mixin, 'drop');
        UIkit.mixin(mixin, 'tooltip');

    }

})(jQuery, UIkit);
