if (typeof jQuery === 'undefined') {
    throw new Error('Page requires jQuery')
}

/* ControlSidebar()
 * ===============
 * Toggles the state of the control sidebar
 *
 * @Usage: $('#control-sidebar-trigger').controlSidebar(options)
 *         or add [data-toggle="control-sidebar"] to the trigger
 *         Pass any option as data-option="value"
 */
+function ($) {
    'use strict'

    var DataKey = 'lte.controlsidebar'

    var Default = {
        slide: true
    }

    var Selector = {
        sidebar: '.control-sidebar',
        data: '[data-toggle="control-sidebar"]',
        open: '.control-sidebar-open',
        bg: '.control-sidebar-bg',
        wrapper: '.wrapper',
        content: '.content-wrapper',
        boxed: '.layout-boxed'
    }

    var ClassName = {
        open: 'control-sidebar-open',
        fixed: 'fixed'
    }

    var Event = {
        collapsed: 'collapsed.controlsidebar',
        expanded: 'expanded.controlsidebar'
    }

    // ControlSidebar Class Definition
    // ===============================
    var ControlSidebar = function (element, options) {
        this.element = element
        this.options = options
        this.hasBindedResize = false

        this.init()
    }

    ControlSidebar.prototype.init = function () {
        // Add click listener if the element hasn't been
        // initialized using the data API
        if (!$(this.element).is(Selector.data)) {
            $(this).on('click', this.toggle)
        }

        this.fix()
        $(window).resize(function () {
            this.fix()
        }.bind(this))
    }

    ControlSidebar.prototype.toggle = function (event) {
        if (event) event.preventDefault()

        this.fix()

        if (!$(Selector.sidebar).is(Selector.open) && !$('body').is(Selector.open)) {
            this.expand()
        } else {
            this.collapse()
        }
    }

    ControlSidebar.prototype.expand = function () {
        if (!this.options.slide) {
            $('body').addClass(ClassName.open)
        } else {
            $(Selector.sidebar).addClass(ClassName.open)
        }

        $(this.element).trigger($.Event(Event.expanded))
    }

    ControlSidebar.prototype.collapse = function () {
        $('body, ' + Selector.sidebar).removeClass(ClassName.open)
        $(this.element).trigger($.Event(Event.collapsed))
    }

    ControlSidebar.prototype.fix = function () {
        if ($('body').is(Selector.boxed)) {
            this._fixForBoxed($(Selector.bg))
        }
    }

    // Private

    ControlSidebar.prototype._fixForBoxed = function (bg) {
        bg.css({
            position: 'absolute',
            height: $(Selector.wrapper).height()
        })
    }

    // Plugin Definition
    // =================
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option)
                $this.data(DataKey, (data = new ControlSidebar($this, options)))
            }

            if (typeof option == 'string') data.toggle()
        })
    }

    var old = $.fn.controlSidebar

    $.fn.controlSidebar = Plugin
    $.fn.controlSidebar.Constructor = ControlSidebar

    // No Conflict Mode
    // ================
    $.fn.controlSidebar.noConflict = function () {
        $.fn.controlSidebar = old
        return this
    }

    // ControlSidebar Data API
    // =======================
    $(document).on('click', Selector.data, function (event) {
        if (event) event.preventDefault()
        Plugin.call($(this), 'toggle')
    })

}(jQuery)

/* Layout()
    * ========
    * Implements admin layout.
    * Fixes the layout height in case min-height fails.
    *
    * @usage activated automatically upon window load.
    *        Configure any options by passing data-option="value"
    *        to the body tag.
    */
+ function ($) {
    'use strict'

    var DataKey = 'lte.layout'

    var Default = {
        slimscroll: true,
        resetHeight: true
    }

    var Selector = {
        wrapper: '.wrapper',
        contentWrapper: '.content-wrapper',
        layoutBoxed: '.layout-boxed',
        mainFooter: '.main-footer',
        mainHeader: '.main-header',
        sidebar: '.sidebar',
        controlSidebar: '.control-sidebar',
        fixed: '.fixed',
        sidebarMenu: '.sidebar-menu',
        logo: '.main-header .logo'
    }

    var ClassName = {
        fixed: 'fixed',
        holdTransition: 'hold-transition'
    }

    var Layout = function (options) {
        this.options = options
        this.bindedResize = false
        this.activate()
    }

    Layout.prototype.activate = function () {
        this.fix()
        this.fixSidebar()

        $('body').removeClass(ClassName.holdTransition)

        if (this.options.resetHeight) {
            $('body, html, ' + Selector.wrapper).css({
                'height': 'auto',
                'min-height': '100%'
            })
        }

        if (!this.bindedResize) {
            $(window).resize(function () {
                this.fix()
                this.fixSidebar()

                $(Selector.logo + ', ' + Selector.sidebar).one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
                    this.fix()
                    this.fixSidebar()
                }.bind(this))
            }.bind(this))

            this.bindedResize = true
        }

        $(Selector.sidebarMenu).on('expanded.tree', function () {
            this.fix()
            this.fixSidebar()
        }.bind(this))

        $(Selector.sidebarMenu).on('collapsed.tree', function () {
            this.fix()
            this.fixSidebar()
        }.bind(this))
    }

    Layout.prototype.fix = function () {
        // Remove overflow from .wrapper if layout-boxed exists
        $(Selector.layoutBoxed + ' > ' + Selector.wrapper).css('overflow', 'hidden')

        // Get window height and the wrapper height
        var footerHeight = $(Selector.mainFooter).outerHeight() || 0
        var neg = $(Selector.mainHeader).outerHeight() + footerHeight
        var windowHeight = $(window).height()
        var sidebarHeight = $(Selector.sidebar).height() || 0

        // Set the min-height of the content and sidebar based on
        // the height of the document.
        if ($('body').hasClass(ClassName.fixed)) {
            $(Selector.contentWrapper).css('min-height', windowHeight - footerHeight)
        } else {
            var postSetHeight

            if (windowHeight >= sidebarHeight) {
                $(Selector.contentWrapper).css('min-height', windowHeight - neg)
                postSetHeight = windowHeight - neg
            } else {
                $(Selector.contentWrapper).css('min-height', sidebarHeight)
                postSetHeight = sidebarHeight
            }

            // Fix for the control sidebar height
            var $controlSidebar = $(Selector.controlSidebar)
            if (typeof $controlSidebar !== 'undefined') {
                if ($controlSidebar.height() > postSetHeight)
                    $(Selector.contentWrapper).css('min-height', $controlSidebar.height())
            }
        }
    }

    Layout.prototype.fixSidebar = function () {
        // Make sure the body tag has the .fixed class
        if (!$('body').hasClass(ClassName.fixed)) {
            if (typeof $.fn.slimScroll !== 'undefined') {
                $(Selector.sidebar).slimScroll({ destroy: true }).height('auto')
            }
            return
        }

        // Enable slimscroll for fixed layout
        if (this.options.slimscroll) {
            if (typeof $.fn.slimScroll !== 'undefined') {
                // Destroy if it exists
                // $(Selector.sidebar).slimScroll({ destroy: true }).height('auto')

                // Add slimscroll
                $(Selector.sidebar).slimScroll({
                    height: ($(window).height() - $(Selector.mainHeader).height()) + 'px',
                    color: 'rgba(0,0,0,0.2)',
                    size: '3px'
                })
            }
        }
    }

    // Plugin Definition
    // =================
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option)
                $this.data(DataKey, (data = new Layout(options)))
            }

            if (typeof option === 'string') {
                if (typeof data[option] === 'undefined') {
                    throw new Error('No method named ' + option)
                }
                data[option]()
            }
        })
    }

    var old = $.fn.layout

    $.fn.layout = Plugin
    $.fn.layout.Constuctor = Layout

    // No conflict mode
    // ================
    $.fn.layout.noConflict = function () {
        $.fn.layout = old
        return this
    }

    // Layout DATA-API
    // ===============
    $(window).on('load', function () {
        Plugin.call($('body'))
    })
}(jQuery)


/* PushMenu()
    * ==========
    * Adds the push menu functionality to the sidebar.
    *
    * @usage: $('.btn').pushMenu(options)
    *          or add [data-toggle="push-menu"] to any button
    *          Pass any option as data-option="value"
    */
+ function ($) {
    'use strict'

    var DataKey = 'lte.pushmenu'

    var Default = {
        collapseScreenSize: 767,
        expandOnHover: false,
        expandTransitionDelay: 200
    }

    var Selector = {
        collapsed: '.sidebar-collapse',
        open: '.sidebar-open',
        mainSidebar: '.main-sidebar',
        contentWrapper: '.content-wrapper',
        searchInput: '.sidebar-form .form-control',
        button: '[data-toggle="push-menu"]',
        mini: '.sidebar-mini',
        expanded: '.sidebar-expanded-on-hover',
        layoutFixed: '.fixed'
    }

    var ClassName = {
        collapsed: 'sidebar-collapse',
        open: 'sidebar-open',
        mini: 'sidebar-mini',
        expanded: 'sidebar-expanded-on-hover',
        expandFeature: 'sidebar-mini-expand-feature',
        layoutFixed: 'fixed'
    }

    var Event = {
        expanded: 'expanded.pushMenu',
        collapsed: 'collapsed.pushMenu'
    }

    // PushMenu Class Definition
    // =========================
    var PushMenu = function (options) {
        this.options = options
        this.init()
    }

    PushMenu.prototype.init = function () {
        if (this.options.expandOnHover
            || ($('body').is(Selector.mini + Selector.layoutFixed))) {
            this.expandOnHover()
            $('body').addClass(ClassName.expandFeature)
        }

        $(Selector.contentWrapper).click(function () {
            // Enable hide menu when clicking on the content-wrapper on small screens
            if ($(window).width() <= this.options.collapseScreenSize && $('body').hasClass(ClassName.open)) {
                this.close()
            }
        }.bind(this))

        // __Fix for android devices
        $(Selector.searchInput).click(function (e) {
            e.stopPropagation()
        })
    }

    PushMenu.prototype.toggle = function () {
        var windowWidth = $(window).width()
        var isOpen = !$('body').hasClass(ClassName.collapsed)

        if (windowWidth <= this.options.collapseScreenSize) {
            isOpen = $('body').hasClass(ClassName.open)
        }

        if (!isOpen) {
            this.open()
        } else {
            this.close()
        }
    }

    PushMenu.prototype.open = function () {
        var windowWidth = $(window).width()

        if (windowWidth > this.options.collapseScreenSize) {
            $('body').removeClass(ClassName.collapsed)
                .trigger($.Event(Event.expanded))
        }
        else {
            $('body').addClass(ClassName.open)
                .trigger($.Event(Event.expanded))
        }
    }

    PushMenu.prototype.close = function () {
        var windowWidth = $(window).width()
        if (windowWidth > this.options.collapseScreenSize) {
            $('body').addClass(ClassName.collapsed)
                .trigger($.Event(Event.collapsed))
        } else {
            $('body').removeClass(ClassName.open + ' ' + ClassName.collapsed)
                .trigger($.Event(Event.collapsed))
        }
    }

    PushMenu.prototype.expandOnHover = function () {
        $(Selector.mainSidebar).hover(function () {
            if ($('body').is(Selector.mini + Selector.collapsed)
                && $(window).width() > this.options.collapseScreenSize) {
                this.expand()
            }
        }.bind(this), function () {
            if ($('body').is(Selector.expanded)) {
                this.collapse()
            }
        }.bind(this))
    }

    PushMenu.prototype.expand = function () {
        setTimeout(function () {
            $('body').removeClass(ClassName.collapsed)
                .addClass(ClassName.expanded)
        }, this.options.expandTransitionDelay)
    }

    PushMenu.prototype.collapse = function () {
        setTimeout(function () {
            $('body').removeClass(ClassName.expanded)
                .addClass(ClassName.collapsed)
        }, this.options.expandTransitionDelay)
    }

    // PushMenu Plugin Definition
    // ==========================
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option)
                $this.data(DataKey, (data = new PushMenu(options)))
            }

            if (option === 'toggle') data.toggle()
        })
    }

    var old = $.fn.pushMenu

    $.fn.pushMenu = Plugin
    $.fn.pushMenu.Constructor = PushMenu

    // No Conflict Mode
    // ================
    $.fn.pushMenu.noConflict = function () {
        $.fn.pushMenu = old
        return this
    }

    // Data API
    // ========
    $(document).on('click', Selector.button, function (e) {
        e.preventDefault()
        Plugin.call($(this), 'toggle')
    })
    $(window).on('load', function () {
        Plugin.call($(Selector.button))
    })
}(jQuery)

/* Tree()
    * ======
    * Converts a nested list into a multilevel
    * tree view menu.
    *
    * @Usage: $('.my-menu').tree(options)
    *         or add [data-widget="tree"] to the ul element
    *         Pass any option as data-option="value"
    */
+ function ($) {
    'use strict'

    var DataKey = 'lte.tree'

    var Default = {
        animationSpeed: 500,
        accordion: true,
        followLink: false,
        trigger: '.treeview a'
    }

    var Selector = {
        tree: '.tree',
        treeview: '.treeview',
        treeviewMenu: '.treeview-menu',
        open: '.menu-open, .active',
        li: 'li',
        data: '[data-widget="tree"]',
        active: '.active'
    }

    var ClassName = {
        open: 'menu-open',
        tree: 'tree'
    }

    var Event = {
        collapsed: 'collapsed.tree',
        expanded: 'expanded.tree'
    }

    // Tree Class Definition
    // =====================
    var Tree = function (element, options) {
        this.element = element
        this.options = options

        $(this.element).addClass(ClassName.tree)

        $(Selector.treeview + Selector.active, this.element).addClass(ClassName.open)

        this._setUpListeners()
    }

    Tree.prototype.toggle = function (link, event) {
        var treeviewMenu = link.next(Selector.treeviewMenu)
        var parentLi = link.parent()
        var isOpen = parentLi.hasClass(ClassName.open)

        if (!parentLi.is(Selector.treeview)) {
            return
        }

        if (!this.options.followLink || link.attr('href') === '#') {
            event.preventDefault()
        }

        if (isOpen) {
            this.collapse(treeviewMenu, parentLi)
        } else {
            this.expand(treeviewMenu, parentLi)
        }
    }

    Tree.prototype.expand = function (tree, parent) {
        var expandedEvent = $.Event(Event.expanded)

        if (this.options.accordion) {
            var openMenuLi = parent.siblings(Selector.open)
            var openTree = openMenuLi.children(Selector.treeviewMenu)
            this.collapse(openTree, openMenuLi)
        }

        parent.addClass(ClassName.open)
        tree.slideDown(this.options.animationSpeed, function () {
            $(this.element).trigger(expandedEvent)
        }.bind(this))
    }

    Tree.prototype.collapse = function (tree, parentLi) {
        var collapsedEvent = $.Event(Event.collapsed)

        tree.find(Selector.open).removeClass(ClassName.open)
        parentLi.removeClass(ClassName.open)
        tree.slideUp(this.options.animationSpeed, function () {
            tree.find(Selector.open + ' > ' + Selector.treeview).slideUp()
            $(this.element).trigger(collapsedEvent)
        }.bind(this))
    }

    // Private

    Tree.prototype._setUpListeners = function () {
        var that = this

        $(this.element).on('click', this.options.trigger, function (event) {
            that.toggle($(this), event)
        })
    }

    // Plugin Definition
    // =================
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option)
                $this.data(DataKey, new Tree($this, options))
            }
        })
    }

    var old = $.fn.tree

    $.fn.tree = Plugin
    $.fn.tree.Constructor = Tree

    // No Conflict Mode
    // ================
    $.fn.tree.noConflict = function () {
        $.fn.tree = old
        return this
    }

    // Tree Data API
    // =============
    $(window).on('load', function () {
        $(Selector.data).each(function () {
            Plugin.call($(this))
        })
    })

}(jQuery)

/* BoxWidget()
    * ======
    * Adds box widget functions to boxes.
    *
    * @Usage: $('.my-box').boxWidget(options)
    *         This plugin auto activates on any element using the `.box` class
    *         Pass any option as data-option="value"
    */
+ function ($) {
    'use strict'

    var DataKey = 'lte.boxwidget'

    var Default = {
        animationSpeed: 500,
        collapseTrigger: '[data-widget="collapse"]',
        removeTrigger: '[data-widget="remove"]',
        collapseIcon: 'fa-chevron-up',
        expandIcon: 'fa-chevron-down',
        removeIcon: 'fa-times'
    }

    var Selector = {
        data: '.box',
        collapsed: '.collapsed-box',
        body: '.box-body',
        footer: '.box-footer',
        tools: '.box-tools'
    }

    var ClassName = {
        collapsed: 'collapsed-box'
    }

    var Event = {
        collapsed: 'collapsed.boxwidget',
        expanded: 'expanded.boxwidget',
        removed: 'removed.boxwidget'
    }

    // BoxWidget Class Definition
    // =====================
    var BoxWidget = function (element, options) {
        this.element = element
        this.options = options

        this._setUpListeners()
    }

    BoxWidget.prototype.toggle = function () {
        var isOpen = !$(this.element).is(Selector.collapsed)

        if (isOpen) {
            this.collapse()
        } else {
            this.expand()
        }
    }

    BoxWidget.prototype.expand = function () {
        var expandedEvent = $.Event(Event.expanded)
        var collapseIcon = this.options.collapseIcon
        var expandIcon = this.options.expandIcon

        $(this.element).removeClass(ClassName.collapsed)

        $(this.element)
            .find(Selector.tools)
            .find('.' + expandIcon)
            .removeClass(expandIcon)
            .addClass(collapseIcon)

        $(this.element).find(Selector.body + ', ' + Selector.footer)
            .slideDown(this.options.animationSpeed, function () {
                $(this.element).trigger(expandedEvent)
            }.bind(this))
    }

    BoxWidget.prototype.collapse = function () {
        var collapsedEvent = $.Event(Event.collapsed)
        var collapseIcon = this.options.collapseIcon
        var expandIcon = this.options.expandIcon

        $(this.element)
            .find(Selector.tools)
            .find('.' + collapseIcon)
            .removeClass(collapseIcon)
            .addClass(expandIcon)

        $(this.element).find(Selector.body + ', ' + Selector.footer)
            .slideUp(this.options.animationSpeed, function () {
                $(this.element).addClass(ClassName.collapsed)
                $(this.element).trigger(collapsedEvent)
            }.bind(this))
    }

    BoxWidget.prototype.remove = function () {
        var removedEvent = $.Event(Event.removed)

        $(this.element).slideUp(this.options.animationSpeed, function () {
            $(this.element).trigger(removedEvent)
            $(this.element).remove()
        }.bind(this))
    }

    // Private

    BoxWidget.prototype._setUpListeners = function () {
        var that = this

        $(this.element).on('click', this.options.collapseTrigger, function (event) {
            if (event) event.preventDefault()
            that.toggle()
        })

        $(this.element).on('click', this.options.removeTrigger, function (event) {
            if (event) event.preventDefault()
            that.remove()
        })
    }

    // Plugin Definition
    // =================
    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option)
                $this.data(DataKey, (data = new BoxWidget($this, options)))
            }

            if (typeof option == 'string') {
                if (typeof data[option] == 'undefined') {
                    throw new Error('No method named ' + option)
                }
                data[option]()
            }
        })
    }

    var old = $.fn.boxWidget

    $.fn.boxWidget = Plugin
    $.fn.boxWidget.Constructor = BoxWidget

    // No Conflict Mode
    // ================
    $.fn.boxWidget.noConflict = function () {
        $.fn.boxWidget = old
        return this
    }

    // BoxWidget Data API
    // ==================
    $(window).on('load', function () {
        $(Selector.data).each(function () {
            Plugin.call($(this))
        })
    })

}(jQuery)

// NProgress
if (typeof NProgress != 'undefined') {
    $(document).ready(function () {
        NProgress.configure({ showSpinner: false });
        NProgress.start();
    });

    $(window).load(function () {
        NProgress.done();
    });
}

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $SIDEBAR_MENU = $('.main-sidebar');

function init_sidebar() {
    // check active menu
    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('active').parents('ul').slideDown().parent().addClass('active');
}

function tableHover() {
    $('body').on('mouseenter', '.cell', function (e) {
        $(this).attr('id', 'current-cell');
        $(this).closest('tr').addClass('highlight');
        var currentSpan = 0;
        if ($(this).closest('tr').attr('span')) {
            currentSpan = parseInt($(this).closest('tr').attr('span'));
        }
        var cellIndex = $(this).index();
        var table = $(this).closest('table');

        if (table.hasClass('span-table')) {
            var rows = table.find('tr');
            for (let index = 0; index < rows.length; index++) {
                const row = rows[index];
                
                if (currentSpan != 0) {
                    if ($(row).hasClass('span-row')) {
                        if (cellIndex == 0) {
                            // first col
                            continue;
                        }
                        $(row).find('.cell:nth-child(' + (cellIndex + 1) + ')').addClass('highlight');
                    } else {
                        if (cellIndex == 0) {
                            // first col
                            continue;
                        }
                        $(row).find('.cell:nth-child(' + (cellIndex+ currentSpan) + ')').addClass('highlight');
                    }
                } else {
                    if ($(row).hasClass('span-row')) {
                        var spanNum = parseInt(row.getAttribute('span'));
                        if (cellIndex < spanNum) {
                            continue;
                        } else {
                            $(row).find('.cell:nth-child(' + (cellIndex - spanNum + 2) + ')').addClass('highlight');
                        }
                    } else {
                        $(row).find('.cell:nth-child(' + (cellIndex + 1) + ')').addClass('highlight');
                    }
                }
            }
        } else {
            $(this).closest('table').find('.cell:nth-child(' + ($(this).index() + 1) + ')').addClass('highlight');
        }
    });
    $('body').on('mouseout', '.cell', function (e) {
        $(this).removeAttr('id');
        $(this).closest('tr').removeClass('highlight');
        var currentSpan = 0;
        if ($(this).closest('tr').attr('span')) {
            currentSpan = parseInt($(this).closest('tr').attr('span'));
        }
        var cellIndex = $(this).index();
        var table = $(this).closest('table');

        if (table.hasClass('span-table')) {
            var rows = table.find('tr');
            for (let index = 0; index < rows.length; index++) {
                const row = rows[index];
                if (currentSpan != 0) {
                    if ($(row).hasClass('span-row')) {
                        if (cellIndex == 0) {
                            // first col
                            continue;
                        }
                        $(row).find('.cell:nth-child(' + (cellIndex + 1) + ')').removeClass('highlight');
                    } else {
                        if (cellIndex == 0) {
                            // first col
                            continue;
                        }
                        $(row).find('.cell:nth-child(' + (cellIndex+ currentSpan) + ')').removeClass('highlight');
                    }
                } else {
                    if ($(row).hasClass('span-row')) {
                        var spanNum = parseInt(row.getAttribute('span'));
                        if (cellIndex < spanNum) {
                            continue;
                        } else {
                            $(row).find('.cell:nth-child(' + (cellIndex - spanNum + 2) + ')').removeClass('highlight');
                        }
                    } else {
                        $(row).find('.cell:nth-child(' + (cellIndex + 1) + ')').removeClass('highlight');
                    }
                }
            }
        } else {
            $(this).closest('table').find('.cell:nth-child(' + ($(this).index() + 1) + ')').removeClass('highlight');
        }
    });
}

function initSelect2() {
    $('.select2-theme').select2({
        placeholder: 'Select a value',
        allowClear: true,
        theme: "bootstrap",
        language: {
            noResults: function() {
                return "Không tìm thấy kết quả";
            }
        }
    });
}

function initDatetimePicker() {
    // init datetime picker
    var elems = Array.prototype.slice.call($('.input-picker'));
    var now = new Date();
    elems.forEach(function (ele) {
        var inputDate = $('#' + ele.id + ' input').val();
        if ($(ele).hasClass('month-mode')) {
            $('#' + ele.id).datetimepicker({
                useCurrent: false,
                viewMode: 'months',
                date: inputDate,
                format: 'YYYY-MM',
                locale: 'vi'
            });
        } else {
            $('#' + ele.id).datetimepicker({
                useCurrent: false,
                date: inputDate,
                format: 'YYYY-MM-DD',
                locale: 'vi'
            });
        }
        if ($(ele).hasClass('gt-now')) {
            $('#' + ele.id).data('DateTimePicker').minDate(now);
        }

        // re-validate when user change picker
        $('#' + ele.id).on('dp.change', function(e) {
            // change form state
            // unsaved = true;
            $('#' + ele.id + ' input').parsley().validate();

            // validate relation input when current input pass the validation
            if ($('#' + ele.id + ' input').parsley().isValid()) {
                var relationEleId;
                if ($('#' + ele.id + ' input').hasClass('from-date-picker')) {
                    relationEleId = $('#' + ele.id + ' input').attr('data-parsley-before-date');
                } else if ($('#' + ele.id + ' input').hasClass('to-date-picker')) {
                    relationEleId = $('#' + ele.id + ' input').attr('data-parsley-after-date');
                }
    
                if (relationEleId) {
                    if ($(relationEleId).hasClass('to-date-picker')) {
                        $(relationEleId).parent().data('DateTimePicker').minDate(e.date);
                    } else if ($(relationEleId).hasClass('from-date-picker')) {
                        $(relationEleId).parent().data('DateTimePicker').maxDate(e.date);
                    }
                    $(relationEleId).parsley().validate();
                }
            }
        });
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (/^image\/\w+$/.test(file.type)) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if ($("#avatar").hasClass('cropper-hidden')){
                    $("#avatar").cropper('destroy');
                }
                $('#avatar').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            ratio = 2 / 3;
            if ($(input).hasClass('square-img')) {
                ratio = 1;
            }
            setTimeout(function() {
                initCropper(ratio)
            }, 1000);
            $('#cropper-modal').modal('toggle');
        } else {
            window.alert('Please choose an image file.');
        }
    } else {
        // clear input
        $('#cropped_result').empty();
        $('input[name="b64code"]').val('');
    }
}

function initCropper(ratio){
    var imgCropper = $('#avatar').cropper({
        aspectRatio: ratio,
        crop: function(e) {
            console.log(e)
        }
    });

    $('#crop-btn').click(function() {
        var imgurl = imgCropper.cropper('getCroppedCanvas').toDataURL();
        var img = document.createElement("img");
        
        img.addEventListener('load', function() {
            // set body height        
            var contentHeight = $('.right_col').height();
            var newHeight = contentHeight + this.height + 1;
            $('.right_col').css('min-height', newHeight);
        });

        img.src = imgurl;
        img.id = 'cropped';
        $('#cropped_result').empty().append(img);
        $('input[name="b64code"]').val(imgurl);
    });
}

function viewStudent(studentId) {
    if (!studentId) {
        studentId = $('#student-name').val();
    }
    window.open('/tvms/students/view/' + studentId, '_blank');
}

// var unsaved = false;
$(document).ready(function() {
    init_sidebar();
    tableHover();
    initSelect2();
    initDatetimePicker();

    $('#filter-refresh-btn').click(function() {
        $('#filter-form')[0].reset();

        // reset select2
        var selectBoxes = $('#filter-form').find('select');
        for (let index = 0; index < selectBoxes.length; index++) {
            const ele = selectBoxes[index];
            if (ele.id == "records") {
                continue
            }
            $('#' + ele.id).val(null).trigger('change');
        }
    });

    $('.select2-theme').change(function(e) {
        $('#' + e.target.id).parsley().validate();
    });
    //custom validator
    window.Parsley.addValidator('beforeDate', {
        validateString: function(value, requirement, parsleyField) {
            var srcDate = new Date(value);
            var dstValue = $(requirement).val();
            if (dstValue === '') {
                return true;
            }
            var dstDate = new Date(dstValue);
            return srcDate <= dstDate;
        },
        messages: {
            en: 'Before end date',
        }
    });

    window.Parsley.addValidator('afterDate', {
        validateString: function(value, requirement, parsleyField) {
            if (value === '') {
                return true;
            }
            var srcDate = new Date(value);
            var dstValue = $(requirement).val();
            if (dstValue === '') {
                return true;
            }
            var dstDate = new Date(dstValue);
            return srcDate >= dstDate;
        },
        messages: {
            en: 'After from date',
        }
    });

    window.Parsley.addValidator('checkEmpty', {
        validateString: function(value, requirement, parsleyField) {
            if (value === '') {
                var currentId = parsleyField.$element[0].id;
                var otherEles = $(requirement).not('#' + currentId);
                for (var index = 0; index < otherEles.length; index++) {
                    if (otherEles[index].value !== '') {
                        return false
                    }
                }
            }
            return true;
        },
        messages: {
            en: 'This value is required.',
        }
    });

    // check save state
    // $("input").change(function(){ //trigers change in all input fields including text type
    //     unsaved = true;
    // });
});

// function unloadPage(){ 
//     if (unsaved){
//         return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
//     }
// }
// window.onbeforeunload = unloadPage;

Handlebars.registerHelper("inc", function (value, options) {
    return parseInt(value) + 1;
});

Handlebars.registerHelper("trans", function (value, options) {
    if (value == 'M') {
        return "Nam";
    }
    return "Nữ";
});

Handlebars.registerHelper("phoneFormat", function (value, options) {
    if (value.length == 10) {
        return value.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
    } else if (value.length == 11) {
        return value.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
    } else {
        return value;
    }
});