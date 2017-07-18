/**
* CLASS HELPERS
* Plain JavaScript functions to add, remove, toggle, and check for classes, no jQuery required
*/

// hasClass
function hasClass(elem, className) {
    return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
}

// addClass
function addClass(elem, className) {
    if (!hasClass(elem, className)) {
        elem.className += ' ' + className;
    }
}

// removeClass
function removeClass(elem, className) {
    var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, ' ') + ' ';
    if (hasClass(elem, className)) {
        while (newClass.indexOf(' ' + className + ' ') >= 0) {
            newClass = newClass.replace(' ' + className + ' ', ' ');
        }
        elem.className = newClass.replace(/^\s+|\s+$/g, '');
    }
}

// toggleClass
function toggleClass(elem, className) {
    var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, " ") + ' ';
    if (hasClass(elem, className)) {
        while (newClass.indexOf(" " + className + " ") >= 0) {
            newClass = newClass.replace(" " + className + " ", " ");
        }
        elem.className = newClass.replace(/^\s+|\s+$/g, '');
    } else {
        elem.className += ' ' + className;
    }
}



// EXAMPLES 

// Example of hasClass usage
// document.getElementById('button').onclick = function() {
//     if (hasClass(document.getElementById('button'), 'superman')) {
//         this.innerHTML = 'Mission success: \'superman\' class exists.';
//     }
// }

// Example of addClass usage
// document.getElementById('button').onclick = function() {
//     addClass(this, 'active');
//     this.innerHTML = 'Woo! Nice work.';
// }

// Example of removeClass usage
// document.getElementById('button').onclick = function() {
//     removeClass(this, 'active');
//     this.innerHTML = 'Yellow is much nicer.';
// }

// Example of toggleClass usage
// document.getElementById('button').onclick = function() {
//     toggleClass(this, 'active');
// }


