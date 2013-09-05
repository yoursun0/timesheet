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

