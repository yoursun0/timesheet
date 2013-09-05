function EventBox($parent, $opts){
    var $EventBox = this;
    
    this.$parent = $parent;
    this.$resizeHandle = $('<div class="ui-icon ui-icon-gripsmall-diagonal-se ui-resizable-se"></div>');
    this.$body = $('<div class="timesheet_eventbox" unselectable="on"></div>').append('<span class="hidden event_id">0</span>')
		.append('<div class="boderTop"></div>')
		.append('<div class="boderBottom"></div>')
		.append('&nbsp;<span class="event_title"><a href="javascript:edit(this);">JXXXX</a></span>')
		.append(' (<span class="start_time">XX:XX</span> - <span class="end_time">XX:XX</span>)')
		//.append('<span class="hidden job_name">-</span>')
		.append('<br /><div class="event_description" style="height:100%;overflow:hidden;"></div>');
	this.$body.append(this.$resizeHandle);
    this.position = {
        top: 0,
        left: 0
    };
    this.size = {
        height: 15,
        width: "100%"
    };
    this.boundary = {
        lower: 0,
        upper: 1440
    };
    this.setParent($parent);
    
	this.$body.find('a').mousedown(function(event){
        if ($opts.editClicked) {
            $opts.editClicked(event,$EventBox);
        }
        event.stopPropagation();
	}),
	
    this.$body.mousedown(function(event){
        if ($opts.beforeDrag) {
            $EventBox.updateBoundary();
            $opts.beforeDrag(event,$EventBox);
        }
        event.stopPropagation();
    }).draggable({
        containment: 'parent',
        scroll: true,
        start: function(){
            $EventBox.updateBoundary();
        },
        drag: function(event, ui){			
            var top = ui.position.top = Math.round(ui.position.top / 15) * 15;
            if ($EventBox.position.top == top) {
                return;
            }
            var height = $EventBox.size.height;
            
            if (top < $EventBox.boundary.lower) {
                top = $EventBox.boundary.lower;
            }
            if (top + height > $EventBox.boundary.upper) {
                top = $EventBox.boundary.upper - height;
            }
            $EventBox.position.top = ui.position.top = top;
			
            $EventBox.updateTime();
        }
		
    });
    this.$resizeHandle.mousedown(function(event){
        if ($opts.beforeResize) {
            $EventBox.updateBoundary();
            $opts.beforeResize(event,$EventBox);
        }
        event.stopPropagation();
    });
}

EventBox.prototype = {
    updateBoundary: function(){//compare other event box and update boundary
        var ubound = 1440;
        var lbound = 0;
        var selfT = this.position.top;
        var selfB = selfT + this.size.height;
        
        this.$body.siblings(".timesheet_eventbox").each(function(){
            var pT = $(this).position().top;
            var pB = pT + $(this).height();
            if (pB >= lbound && pB <= selfT) {
                lbound = pB;
            }
            if (pT <= ubound && pT >= selfB) {
                ubound = pT;
            }
        });
        this.boundary = {
            lower: lbound,
            upper: ubound
        };
    },
    updateSize: function(pointA, pointB){
        var ubound = this.boundary.upper;
        var lbound = this.boundary.lower;
        var height, top;
        
        pointA = pointA - (pointA % 15);
        pointB = pointB < 0 ? 0 : pointB - (pointB % 15);
        
        if (pointB < lbound) {
            pointB = lbound;
        }
        if (pointB > ubound) {
            pointB = ubound;
        }
        if (pointB < pointA) {
            height = pointA - pointB + 15;
            top = pointB;
        } else {
            height = pointB - pointA + 15;
            top = pointA;
        }
        if (lbound > top) {
            top = lbound;
        }
        if (ubound < (height + top)) {
            height = ubound - top;
        }
        
        this.setPosition(top);
        this.setSize(height);
		
		this.$body.find('.event_description').height(height - 15)

    },
    updateTime: function(){
        var p1, p2;//this.$body.position().top
        p2 = (p1 = this.position.top) + this.$body.height();
        this.$body.find(".start_time:first").text(parseInt(p1 / 60).zf(2) + ":" + (p1 % 60).zf(2));
        this.$body.find(".end_time:first").text(parseInt(p2 / 60).zf(2) + ":" + (p2 % 60).zf(2));
    },
    setTime: function(start, end){
        this.setSize(end - start);
        this.setPosition(start);
    },
    setSize: function(height, width){
        this.size.height = height;
        if (width) {
            this.size.width = width;
        }
        this.$body.css(this.size);
        this.updateTime();
    },
    setPosition: function(top, left){
        this.position.top = top
        if (left) {
            this.position.left = left
        }
        this.$body.css(this.position);
        this.updateTime();
    },
    setParent: function($parent){
        this.$body.appendTo($parent);
        this.updateBoundary();
    },
    setType: function(str){
		var color = '#DFEBFF';
		if (str == 'A') {
			color = '#FFCCFF';
		} else if (str == 'B') {
			color = '#99CCCC';
		} else if (str == 'Z') {
			color = '#CCCCCC';
		}
		this.$body.css('background-color',color)
    },
    setId: function(str){
        this.$body.find(".event_id").html(str);
    },
    setTitle: function(str){
        this.$body.find(".event_title a").text(str);
    },
    setDescription: function(str){
        this.$body.find(".event_description").text(str);
    },
    getId: function(id){
        return this.$body.find(".event_id").html();
    },
    getTitle: function(){
        return this.$body.find(".event_title a").text();
    },
    getDescription: function(str){
        return this.$body.find(".event_description").text();
    },
    getParams: function(){
		var columnParam = window["eval"]("(" + this.$body.siblings('.timesheet_columnParam').html() + ")");
        return $.param({
            id: this.getId(),
            name: this.getTitle(),
            description: this.getDescription(),
            start_time: (this.position.top * 60 + columnParam.date),
            end_time: (this.size.height + this.position.top)  * 60 + columnParam.date
        });
    },
    show: function(){
        this.$body.show();
    },
    hide: function(){
        this.$body.hide();
    },
	remove: function() {
		this.$body.remove();
		$EventBox = null;
	}
}
