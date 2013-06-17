var s, hangman = {
	settings: {
		alphabet: [ ['a', 65], ['b', 66], ['c', 67], ['d', 68], ['e', 69], ['f', 70], ['g', 71], ['h', 72], ['i', 73], ['j', 74], ['k', 75], ['l', 76], ['m', 77], ['n', 78], ['o', 79], ['p', 80], ['q', 81], ['r', 82], ['s', 83], ['t', 84], ['u', 85], ['v', 86], ['w', 87], ['x', 88], ['y', 89], ['z', 90], ['-', 109] ],
	},

	init: function() {
		s = this.settings;
		this.bindUIactions();
	},

	findItem: function(source, needle) {
		for (var i = 0, len = source.length; i < len; i++) {
    		if (source[i][1] === needle) return source[i];
		}
		return false;
	},

	bindUIactions: function() {
		document.onkeyup = function(e) {
			var item = hangman.findItem(s.alphabet, e.which);
			if(item !== false) {
				var el = document.querySelectorAll('.alphabet a');
				for(var i = 0, len = el.length; i < len; i++) {
					if(el[i].innerHTML === item[0].toUpperCase()) {
						window.location.href = el[i].href;
						return;
					}
				}
			}
		}
	}
}

hangman.init();