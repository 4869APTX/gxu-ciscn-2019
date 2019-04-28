/*!
 * static/js/webftp.util.js
 * Util工具
 */

app.util = {};
app.util.cookie = function (name, value, options) {
    var name = 'webftp_' + name;
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                //date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                date.setTime(date.getTime() + (options.expires * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

app.util.md5 = function (str) {
    $_this = app.util.md5;
    $_this.hexcase = 0; //hex output format. 0 - lowercase; 1 - uppercase
    $_this.b64pad = ''; //base-64 pad character. "=" for strict RFC compliance
    $_this.chrsz = 8; // bits per input character. 8 - ASCII; 16 - Unicode
    $_this.core_md5 = function (x, len) {
        x[len >> 5] |= 0x80 << ((len) % 32);
        x[(((len + 64) >>> 9) << 4) + 14] = len;
        var a = 1732584193;
        var b = -271733879;
        var c = -1732584194;
        var d = 271733878;
        for (var i = 0; i < x.length; i += 16) {
            var olda = a;
            var oldb = b;
            var oldc = c;
            var oldd = d;
            a = md5_ff(a, b, c, d, x[i + 0], 7, -680876936);
            d = md5_ff(d, a, b, c, x[i + 1], 12, -389564586);
            c = md5_ff(c, d, a, b, x[i + 2], 17, 606105819);
            b = md5_ff(b, c, d, a, x[i + 3], 22, -1044525330);
            a = md5_ff(a, b, c, d, x[i + 4], 7, -176418897);
            d = md5_ff(d, a, b, c, x[i + 5], 12, 1200080426);
            c = md5_ff(c, d, a, b, x[i + 6], 17, -1473231341);
            b = md5_ff(b, c, d, a, x[i + 7], 22, -45705983);
            a = md5_ff(a, b, c, d, x[i + 8], 7, 1770035416);
            d = md5_ff(d, a, b, c, x[i + 9], 12, -1958414417);
            c = md5_ff(c, d, a, b, x[i + 10], 17, -42063);
            b = md5_ff(b, c, d, a, x[i + 11], 22, -1990404162);
            a = md5_ff(a, b, c, d, x[i + 12], 7, 1804603682);
            d = md5_ff(d, a, b, c, x[i + 13], 12, -40341101);
            c = md5_ff(c, d, a, b, x[i + 14], 17, -1502002290);
            b = md5_ff(b, c, d, a, x[i + 15], 22, 1236535329);
            a = md5_gg(a, b, c, d, x[i + 1], 5, -165796510);
            d = md5_gg(d, a, b, c, x[i + 6], 9, -1069501632);
            c = md5_gg(c, d, a, b, x[i + 11], 14, 643717713);
            b = md5_gg(b, c, d, a, x[i + 0], 20, -373897302);
            a = md5_gg(a, b, c, d, x[i + 5], 5, -701558691);
            d = md5_gg(d, a, b, c, x[i + 10], 9, 38016083);
            c = md5_gg(c, d, a, b, x[i + 15], 14, -660478335);
            b = md5_gg(b, c, d, a, x[i + 4], 20, -405537848);
            a = md5_gg(a, b, c, d, x[i + 9], 5, 568446438);
            d = md5_gg(d, a, b, c, x[i + 14], 9, -1019803690);
            c = md5_gg(c, d, a, b, x[i + 3], 14, -187363961);
            b = md5_gg(b, c, d, a, x[i + 8], 20, 1163531501);
            a = md5_gg(a, b, c, d, x[i + 13], 5, -1444681467);
            d = md5_gg(d, a, b, c, x[i + 2], 9, -51403784);
            c = md5_gg(c, d, a, b, x[i + 7], 14, 1735328473);
            b = md5_gg(b, c, d, a, x[i + 12], 20, -1926607734);
            a = md5_hh(a, b, c, d, x[i + 5], 4, -378558);
            d = md5_hh(d, a, b, c, x[i + 8], 11, -2022574463);
            c = md5_hh(c, d, a, b, x[i + 11], 16, 1839030562);
            b = md5_hh(b, c, d, a, x[i + 14], 23, -35309556);
            a = md5_hh(a, b, c, d, x[i + 1], 4, -1530992060);
            d = md5_hh(d, a, b, c, x[i + 4], 11, 1272893353);
            c = md5_hh(c, d, a, b, x[i + 7], 16, -155497632);
            b = md5_hh(b, c, d, a, x[i + 10], 23, -1094730640);
            a = md5_hh(a, b, c, d, x[i + 13], 4, 681279174);
            d = md5_hh(d, a, b, c, x[i + 0], 11, -358537222);
            c = md5_hh(c, d, a, b, x[i + 3], 16, -722521979);
            b = md5_hh(b, c, d, a, x[i + 6], 23, 76029189);
            a = md5_hh(a, b, c, d, x[i + 9], 4, -640364487);
            d = md5_hh(d, a, b, c, x[i + 12], 11, -421815835);
            c = md5_hh(c, d, a, b, x[i + 15], 16, 530742520);
            b = md5_hh(b, c, d, a, x[i + 2], 23, -995338651);
            a = md5_ii(a, b, c, d, x[i + 0], 6, -198630844);
            d = md5_ii(d, a, b, c, x[i + 7], 10, 1126891415);
            c = md5_ii(c, d, a, b, x[i + 14], 15, -1416354905);
            b = md5_ii(b, c, d, a, x[i + 5], 21, -57434055);
            a = md5_ii(a, b, c, d, x[i + 12], 6, 1700485571);
            d = md5_ii(d, a, b, c, x[i + 3], 10, -1894986606);
            c = md5_ii(c, d, a, b, x[i + 10], 15, -1051523);
            b = md5_ii(b, c, d, a, x[i + 1], 21, -2054922799);
            a = md5_ii(a, b, c, d, x[i + 8], 6, 1873313359);
            d = md5_ii(d, a, b, c, x[i + 15], 10, -30611744);
            c = md5_ii(c, d, a, b, x[i + 6], 15, -1560198380);
            b = md5_ii(b, c, d, a, x[i + 13], 21, 1309151649);
            a = md5_ii(a, b, c, d, x[i + 4], 6, -145523070);
            d = md5_ii(d, a, b, c, x[i + 11], 10, -1120210379);
            c = md5_ii(c, d, a, b, x[i + 2], 15, 718787259);
            b = md5_ii(b, c, d, a, x[i + 9], 21, -343485551);
            a = md5_safe_add(a, olda);
            b = md5_safe_add(b, oldb);
            c = md5_safe_add(c, oldc);
            d = md5_safe_add(d, oldd);
        }
        return Array(a, b, c, d);
    };
    // These functions implement the four basic operations the algorithm uses.
    function md5_cmn(q, a, b, x, s, t) {
        return md5_safe_add(bit_rol(md5_safe_add(md5_safe_add(a, q), md5_safe_add(x, t)), s), b);
    }
    function md5_ff(a, b, c, d, x, s, t) {
        return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }
    function md5_gg(a, b, c, d, x, s, t) {
        return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }
    function md5_hh(a, b, c, d, x, s, t) {
        return md5_cmn(b^c^d, a, b, x, s, t);
    }
    function md5_ii(a, b, c, d, x, s, t) {
        return md5_cmn(c^(b | (~d)), a, b, x, s, t);
    }
    //Add integers, wrapping at 2^32. This uses 16-bit operations internally to work around bugs in some JS interpreters.
    function md5_safe_add(x, y) {
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    }
    //Bitwise rotate a 32-bit number to the left.
    function bit_rol(num, cnt) {
        return (num << cnt) | (num >>> (32 - cnt));
    }
    // Convert a string to an array of little-endian words
    //If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
    $_this.str2binl = function (str) {
        var bin = Array();
        var mask = (1 << $_this.chrsz) - 1;
        for (var i = 0; i < str.length * $_this.chrsz; i += $_this.chrsz) {
            bin[i >> 5] |= (str.charCodeAt(i / $_this.chrsz) & mask) << (i % 32);
        }
        return bin;
    }

    //Convert an array of little-endian words to a hex string.
    $_this.binl2hex = function (binarray) {
        var hex_tab = $_this.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var str = '';
        for (var i = 0; i < binarray.length * 4; i++) {
            str += hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8 + 4)) & 0xF) + hex_tab.charAt((binarray[i >> 2] >> ((i % 4) * 8)) & 0xF);
        }
        return str;
    }
    $_this.hex_md5 = function (s) {
        return $_this.binl2hex($_this.core_md5($_this.str2binl(s), s.length * $_this.chrsz));
    }
    return $_this.hex_md5(str);
}

function get_chmod_num() {
    return [$(':input[name="chmod_deep"]:checked').val(), $('#num_chmod').val()];
}

function set_chmod_num() {
    var chmod = 10000;
    $("[id^='chmod_']").each(function () {
        if ($(this).attr('checked')) {
            chmod = chmod + parseInt($(this).val());
        };
    });

    chmod = ('' + chmod).substr(1);
    $('#num_chmod').val(chmod);
}

function set_chmod_deep(chmod) {
    var chmod = chmod || 755;
    var $chmod_owner = parseInt(chmod / 100),
    $chmod_group = parseInt(chmod / 10 % 10),
    $chmod_public = parseInt(chmod % 10);
    switch ($chmod_owner) {
    case 0:
        break;
    case 1:
        $('#chmod_owner_exec').attr('checked', true);
        break;
    case 2:
        $('#chmod_owner_write').attr('checked', true);
        break;
    case 3:
        $('#chmod_owner_exec').attr('checked', true);
        $('#chmod_owner_write').attr('checked', true);
        break;
    case 4:
        $('#chmod_owner_read').attr('checked', true);
        break;
    case 5:
        $('#chmod_owner_exec').attr('checked', true);
        $('#chmod_owner_read').attr('checked', true);
        break;
    case 6:
        $('#chmod_owner_write').attr('checked', true);
        $('#chmod_owner_read').attr('checked', true);
        break;
    case 7:
        $('#chmod_owner_exec').attr('checked', true);
        $('#chmod_owner_write').attr('checked', true);
        $('#chmod_owner_read').attr('checked', true);
        break;
    }
    switch ($chmod_group) {
    case 0:
        break;
    case 1:
        $('#chmod_group_exec').attr('checked', true);
        break;
    case 2:
        $('#chmod_group_write').attr('checked', true);
        break;
    case 3:
        $('#chmod_group_exec').attr('checked', true);
        $('#chmod_group_write').attr('checked', true);
        break;
    case 4:
        $('#chmod_group_read').attr('checked', true);
        break;
    case 5:
        $('#chmod_group_exec').attr('checked', true);
        $('#chmod_group_read').attr('checked', true);
        break;
    case 6:
        $('#chmod_group_write').attr('checked', true);
        $('#chmod_group_read').attr('checked', true);
        break;
    case 7:
        $('#chmod_group_exec').attr('checked', true);
        $('#chmod_group_write').attr('checked', true);
        $('#chmod_group_read').attr('checked', true);
        break;
    }
    switch ($chmod_public) {
    case 0:
        break;
    case 1:
        $('#chmod_public_exec').attr('checked', true);
        break;
    case 2:
        $('#chmod_public_write').attr('checked', true);
        break;
    case 3:
        $('#chmod_public_exec').attr('checked', true);
        $('#chmod_public_write').attr('checked', true);
        break;
    case 4:
        $('#chmod_public_read').attr('checked', true);
        break;
    case 5:
        $('#chmod_public_exec').attr('checked', true);
        $('#chmod_public_read').attr('checked', true);
        break;
    case 6:
        $('#chmod_public_write').attr('checked', true);
        $('#chmod_public_read').attr('checked', true);
        break;
    case 7:
        $('#chmod_public_exec').attr('checked', true);
        $('#chmod_public_write').attr('checked', true);
        $('#chmod_public_read').attr('checked', true);
        break;
    }
}
