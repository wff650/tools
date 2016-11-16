;(function (win, doc, $, undefined) {

    var conf = {
        'name': 'suifang',
    }

    var lib = {}

    lib.each = function (a, f) {
        if (!a || !f)return;
        var i = 0;
        var l = a.length;
        for (; i < l; i++) {
            f(a[i], i)
        }
    };
    lib.eacho = function (o, f) {
        if (!o || !f)return;
        for (var i in o) {
            if (!o.hasOwnProperty(i)) continue;
            f(o[i], i);
        }
    };
    lib.in_array = function (search, array) {
        for (var i in array) {
            if (array[i] == search) {
                return true;
            }
        }
        return false;
    };

    var self = {};
    self.conf = conf;
    self.bind = function () {

    };

    self.init = function (conf) {
        self.setOptions(conf);
        self.bind();

    };

    self.setOptions = function (conf) {
        self.conf.data = conf.data;
    };
    //注册插件
    win[conf.name] = self;

})(window, document, window.jQuery);
