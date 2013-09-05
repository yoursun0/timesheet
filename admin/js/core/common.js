String.prototype.zf = function(l){
    return '0'.string(l - this.length) + this;
}
String.prototype.string = function(l){
    var s = '', i = 0;
    while (i++ < l) {
        s += this;
    }
    return s;
}
Number.prototype.zf = function(l){
    return this.toString().zf(l);
}

function trimLeft(s){
	for (var i=0; i < s.length; i++){
		if (s.substr(i,1) != ' ') return s.substr(i);
	}
	return('');		
}
function trimRight(s){
	for (var i=s.length; i > 0; i--){
		if (s.substr(i-1,1) != ' ') return s.substr(0,i);
	}
	return('');
}
function trimStr(s){
	return trimLeft(trimRight(s));
}

function stopEvent(e){
    if(window.event){window.event.cancelBubble=true;}else{e.stopPropagation()};
}

function any(val) {
	return true;
}
function isEmpty(str){
	if (str== null){return true;}return(trimStr(str).length==0);
}
function isFunction(func){
	if (func == null) {
		return false;
	}
	return (typeof func == 'function');
}

function cs_handleResponse(json,successCallback,failCallback) {
	if(json && typeof json != 'object') {
		json = window["eval"]("(" + json + ")");
	}
	if(json.type) {
		if (json.msg) {
			alert(json.msg);
		}
		switch(json.type) {
			case 'warn','error':
				if(isFunction(failCallback)) {
					failCallback(json.params || {});
				}
				break;
			case 'ok':
				if(isFunction(successCallback)) {
					successCallback(json.params || {});
				}
				break;
			default:
				break;
		}
	} else {
		alert('Unknown Response');
	}
}

var cs_Ajax_Post = function() {
	
}
