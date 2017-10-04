/*!
 * SwipeView v0.10 ~ Copyright (c) 2011 Matteo Spinelli, http://cubiq.org
 * Released under MIT license, http://cubiq.org/license
 */

var active_SwipeView = null;

var SwipeView = (function(){
    var hasTouch = 'ontouchstart' in window,
        resizeEvent = 'onorientationchange' in window ? 'orientationchange' : 'resize',
        startEvent = hasTouch ? 'touchstart' : 'mousedown',
        moveEvent = hasTouch ? 'touchmove' : 'mousemove',
        endEvent = hasTouch ? 'touchend' : 'mouseup',
        cancelEvent = hasTouch ? 'touchcancel' : 'mouseup',
        isDefined = function( val ) { return val != undefined; },
        webkitTrans = {
            transform: 'webkitTransform',
            transitionDuration: 'webkitTransitionDuration',
            transitionTimingFunction: 'webkitTransitionTimingFunction',
            listener: 'webkitTransitionEnd'
        },
        mozTrans = {
            transform: 'MozTransform',
            transitionDuration: 'MozTransitionDuration',
            transitionTimingFunction: 'MozTransitionTimingFunction',
            listener: 'transitionend'
        },
        oTrans = {
            transform: 'OTransform',
            transitionDuration: 'OTransitionDuration',
            transitionTimingFunction: 'OTransitionTimingFunction',
            listener: 'OTransitionEnd'
        },
        msTrans = {
            transform: 'msTransform',
            transitionDuration: 'msTransitionDuration',
            transitionTimingFunction: 'msTransitionTimingFunction',
            listener: 'msTransitionEnd'
        },
        vendor = isDefined( document.body.style.webkitTransform ) ? webkitTrans :
                isDefined( document.body.style.MozTransform ) ? mozTrans :
                isDefined( document.body.style.OTransform ) ? oTrans :
                isDefined( document.body.style.msTransform ) ?  msTrans :
                undefined;

        SwipeView = function (el, options) {
            var i,
                div,
                className,
                pageIndex;

            this.wrapper = typeof el == 'string' ? document.querySelector(el) : el;
            this.options = {
                text: null,
                numberOfPages: 3,
                snapThreshold: null,
                hastyPageFlip: false,
                loop: true,
                handleMouseSwiping: true,
                id: "swipeview"
            };

            // User defined options
            for (i in options) this.options[i] = options[i];

            this.wrapper.style.overflow = 'hidden';
            this.wrapper.style.position = 'relative';

            this.masterPages = [];

            div = document.createElement('div');
            div.id = this.options.id + '-slider';
            div.style.cssText = 'position:relative;top:0;height:100%;width:100%;';
            if ( vendor ) {
                div.style[vendor.transitionDuration] = 0;
                div.style[vendor.transitionTimingFunction] = "ease-out";
                div.style[vendor.transform] = "translateX(0)";
            }
            this.wrapper.appendChild(div);
            this.slider = div;

            this.refreshSize();

            for (i=-1; i<2; i++) {
                div = document.createElement('div');
                div.id = this.options.id + '-masterpage-' + (i+1);

                div.style.cssText = 'position:absolute;top:0;height:100%;width:100%;left:' + i*100 + '%';
                if ( vendor )
                    div.style[vendor.transform] = "translateZ(0)";

                if (!div.dataset) div.dataset = {};
                pageIndex = i == -1 ? this.options.numberOfPages - 1 : i;
                div.dataset.pageIndex = pageIndex;
                div.dataset.upcomingPageIndex = pageIndex;

                if (!this.options.loop && i == -1) div.style.visibility = 'hidden';

                this.slider.appendChild(div);
                this.masterPages.push(div);
            }

            className = this.masterPages[1].className;
            this.masterPages[1].className = !className ? 'swipeview-active' : className + ' swipeview-active';

            if ( hasTouch || this.options.handleMouseSwiping ) {
                window.addEventListener(resizeEvent, this, false);
                this.wrapper.addEventListener(startEvent, this, false);
                this.wrapper.addEventListener(moveEvent, this, false);
                this.wrapper.addEventListener(endEvent, this, false);
            }

            if ( vendor )
                this.slider.addEventListener( vendor.listener, this, false );

/*            if (!hasTouch) {
                this.wrapper.addEventListener('mouseout', this, false);
            }*/
        };

    SwipeView.prototype = {
        currentMasterPage: 1,
        x: 0,
        page: 0,
        pageIndex: 0,
        customEvents: [],

        onFlip: function (fn) {
            this.wrapper.addEventListener('swipeview-flip', fn, false);
            this.customEvents.push(['flip', fn]);
        },

        onClick: function(fn) {
            this.wrapper.addEventListener('swipeview-click', fn, false);
            this.customEvents.push(['click', fn]);
        },

        onMoveOut: function (fn) {
            this.wrapper.addEventListener('swipeview-moveout', fn, false);
            this.customEvents.push(['moveout', fn]);
        },

        onMoveIn: function (fn) {
            this.wrapper.addEventListener('swipeview-movein', fn, false);
            this.customEvents.push(['movein', fn]);
        },

        onTouchStart: function (fn) {
            this.wrapper.addEventListener('swipeview-touchstart', fn, false);
            this.customEvents.push(['touchstart', fn]);
        },

        destroy: function () {
            while ( this.customEvents.length ) {
                this.wrapper.removeEventListener('swipeview-' + this.customEvents[0][0], this.customEvents[0][1], false);
                this.customEvents.shift();
            }

            // Remove the event listeners
            window.removeEventListener(resizeEvent, this, false);
            this.wrapper.removeEventListener(startEvent, this, false);
            this.wrapper.removeEventListener(moveEvent, this, false);
            this.wrapper.removeEventListener(endEvent, this, false);

            if ( vendor )
                this.slider.removeEventListener( vendor.listener, this, false );

/*            if (!hasTouch) {
                this.wrapper.removeEventListener('mouseout', this, false);
            }*/
        },

        refreshSize: function () {
            this.wrapperWidth = this.wrapper.clientWidth;
            this.wrapperHeight = this.wrapper.clientHeight;
            this.pageWidth = this.wrapperWidth;
            this.maxX = -this.options.numberOfPages * this.pageWidth + this.wrapperWidth;
            this.snapThreshold = this.options.snapThreshold === null ?
                Math.round(this.pageWidth * 0.15) :
                /%/.test(this.options.snapThreshold) ?
                    Math.round(this.pageWidth * this.options.snapThreshold.replace('%', '') / 100) :
                    this.options.snapThreshold;
        },

        updatePageCount: function (n) {
            this.options.numberOfPages = n;
            this.maxX = -this.options.numberOfPages * this.pageWidth + this.wrapperWidth;
        },

        goToPage: function (p) {
            var i;

            this.masterPages[this.currentMasterPage].className = this.masterPages[this.currentMasterPage].className.replace(/(^|\s)swipeview-active(\s|$)/, '');
            for (i=0; i<3; i++) {
                className = this.masterPages[i].className;
                /(^|\s)swipeview-loading(\s|$)/.test(className) || (this.masterPages[i].className = !className ? 'swipeview-loading' : className + ' swipeview-loading');
            }

            p = p < 0 ? 0 : p > this.options.numberOfPages-1 ? this.options.numberOfPages-1 : p;
            this.page = p;
            this.pageIndex = p;

            if ( vendor )
                this.slider.style[vendor.transitionDuration] = '0';

            this.__pos(-p * this.pageWidth);

            this.currentMasterPage = (this.page + 1) - Math.floor((this.page + 1) / 3) * 3;

            this.masterPages[this.currentMasterPage].className = this.masterPages[this.currentMasterPage].className + ' swipeview-active';

            if (this.currentMasterPage == 0) {
                this.masterPages[2].style.left = this.page * 100 - 100 + '%';
                this.masterPages[0].style.left = this.page * 100 + '%';
                this.masterPages[1].style.left = this.page * 100 + 100 + '%';
                this.masterPages[2].dataset.upcomingPageIndex = this.page === 0 ? this.options.numberOfPages-1 : this.page - 1;
                this.masterPages[0].dataset.upcomingPageIndex = this.page;
                this.masterPages[1].dataset.upcomingPageIndex = this.page == this.options.numberOfPages-1 ? 0 : this.page + 1;
            } else if (this.currentMasterPage == 1) {
                this.masterPages[0].style.left = this.page * 100 - 100 + '%';
                this.masterPages[1].style.left = this.page * 100 + '%';
                this.masterPages[2].style.left = this.page * 100 + 100 + '%';

                this.masterPages[0].dataset.upcomingPageIndex = this.page === 0 ? this.options.numberOfPages-1 : this.page - 1;
                this.masterPages[1].dataset.upcomingPageIndex = this.page;
                this.masterPages[2].dataset.upcomingPageIndex = this.page == this.options.numberOfPages-1 ? 0 : this.page + 1;
            } else {
                this.masterPages[1].style.left = this.page * 100 - 100 + '%';
                this.masterPages[2].style.left = this.page * 100 + '%';
                this.masterPages[0].style.left = this.page * 100 + 100 + '%';

                this.masterPages[1].dataset.upcomingPageIndex = this.page === 0 ? this.options.numberOfPages-1 : this.page - 1;
                this.masterPages[2].dataset.upcomingPageIndex = this.page;
                this.masterPages[0].dataset.upcomingPageIndex = this.page == this.options.numberOfPages-1 ? 0 : this.page + 1;
            }

            this.__flip();
        },

        next: function () {
            if (!this.options.loop && this.x == this.maxX) return;

            this.directionX = -1;
            this.x -= 1;
            this.__checkPosition();
        },

        prev: function () {
            if (!this.options.loop && this.x === 0) return;

            this.directionX = 1;
            this.x += 1;
            this.__checkPosition();
        },

        handleEvent: function (e) {
            switch (e.type) {
                case startEvent:
                    this.__start(e);
                    break;
                case moveEvent:
                    this.__move(e);
                    break;
                case cancelEvent:
                case endEvent:
                    this.__end(e);
                    break;
                case resizeEvent:
                    this.__resize();
                    break;
                case 'webkitTransitionEnd':
                case 'transitionend':
                case 'OTransitionEnd':
                case 'MSTransitionEnd':
                    if (e.target == this.slider && !this.options.hastyPageFlip) this.__flip();
                    break;
            }
        },


        /**
        *
        * Pseudo private methods
        *
        */
        __pos: function (x) {
            this.x = x;

            if ( vendor )
                this.slider.style[vendor.transform] = 'translateX(' + x + 'px)';
        },

        __resize: function () {
            this.refreshSize();

            if ( vendor )
                this.slider.style[vendor.transitionDuration] = '0';

            this.__pos(-this.page * this.pageWidth);
        },

        __start: function (e) {
            //e.preventDefault();

            if ( this.initiated || ( active_SwipeView != null && this != active_SwipeView ) ) return;

            var point = hasTouch ? e.touches[0] : e;

            active_SwipeView = this;
            this.initiated = true;
            this.moved = false;
            this.thresholdExceeded = false;
            this.startX = point.pageX;
            this.startY = point.pageY;
            this.pointX = point.pageX;
            this.pointY = point.pageY;
            this.stepsX = 0;
            this.stepsY = 0;
            this.directionX = 0;
            this.directionLocked = false;

/*            var matrix = getComputedStyle(this.slider, null).webkitTransform.replace(/[^0-9-.,]/g, '').split(',');
            this.x = matrix[4] * 1;*/

            if ( vendor )
                this.slider.style[vendor.transitionDuration] = '0';

            this.__event('touchstart');
        },

        __move: function (e) {
            if (!this.initiated) return;

            var point = hasTouch ? e.touches[0] : e,
                deltaX = point.pageX - this.pointX,
                deltaY = point.pageY - this.pointY,
                newX = this.x + deltaX,
                dist = Math.abs(point.pageX - this.startX);

            this.moved = true;
            this.pointX = point.pageX;
            this.pointY = point.pageY;
            if (deltaX !== 0) {
                this.directionX = deltaX > 0 ? 1 : -1;
            }
            this.stepsX += Math.abs(deltaX);
            this.stepsY += Math.abs(deltaY);

            // We take a 10px buffer to figure out the direction of the swipe
            if (this.stepsX < 10 && this.stepsY < 10) {
//                e.preventDefault();
                return;
            }

            // We are scrolling vertically, so skip SwipeView and give the control back to the browser
            if (!this.directionLocked && this.stepsY > this.stepsX) {
                active_SwipeView = null;
                this.initiated = false;
                return;
            }

            e.preventDefault();

            this.directionLocked = true;

            if (!this.options.loop && (newX > 0 || newX < this.maxX)) {
                newX = this.x + (deltaX / 2);
            }

            if (!this.thresholdExceeded && dist >= this.snapThreshold) {
                this.thresholdExceeded = true;
                this.__event('moveout');
            } else if (this.thresholdExceeded && dist < this.snapThreshold) {
                this.thresholdExceeded = false;
                this.__event('movein');
            }

/*            if (newX > 0 || newX < this.maxX) {
                newX = this.x + (deltaX / 2);
            }*/

            this.__pos(newX);
        },

        __end: function (e) {
            if (!this.initiated) return;

            var point = hasTouch ? e.changedTouches[0] : e,
                dist = point.pageX - this.startX;

            this.initiated = false;

            if (!this.moved) {
                active_SwipeView = null;
                this.__event('click');
                return;
            }

            // wait a couple of milliseconds before unsetting in order to be still
            // set for click events checking whether we're swiping or not.
            window.setTimeout( function() {
                    active_SwipeView = null;
                }, 500 );

            if (!this.options.loop && (this.x > 0 || this.x < this.maxX)) {
                dist = 0;
                this.__event('movein');
            }

            // Check if we exceeded the snap threshold
            if (Math.abs(dist) < this.snapThreshold) {
                if ( vendor )
                    this.slider.style[vendor.transitionDuration] = Math.floor(300 * Math.abs(dist) / this.snapThreshold) + 'ms';
                    
                this.__pos(-this.page * this.pageWidth);
                return;
            }
            // Check if swipe was cancelled by reversing swipe direction
            if ((dist < 0 && this.directionX >= 0) || (dist > 0 && this.directionX <= 0)) {
                if ( vendor )
                    this.slider.style[vendor.transitionDuration] = Math.floor(300 * Math.abs(dist) / this.pageWidth) + 'ms';
                
                this.__pos(-this.page * this.pageWidth);
                return;
            }

            this.__checkPosition();
        },

        __checkPosition: function () {
            var pageFlip,
                pageFlipIndex,
                className;

            // could happen on IE9
            if ( this.pageWidth == 0 )
                this.refreshSize();

            this.masterPages[this.currentMasterPage].className = this.masterPages[this.currentMasterPage].className.replace(/(^|\s)swipeview-active(\s|$)/, '');

            // Flip the page
            if (this.directionX > 0) {
                this.page = -Math.ceil(this.x / this.pageWidth);
                this.currentMasterPage = (this.page + 1) - Math.floor((this.page + 1) / 3) * 3;
                this.pageIndex = this.pageIndex === 0 ? this.options.numberOfPages - 1 : this.pageIndex - 1;

                pageFlip = this.currentMasterPage - 1;
                pageFlip = pageFlip < 0 ? 2 : pageFlip;
                this.masterPages[pageFlip].style.left = this.page * 100 - 100 + '%';

                pageFlipIndex = this.page - 1;
            } else {
                this.page = -Math.floor(this.x / this.pageWidth);
                this.currentMasterPage = (this.page + 1) - Math.floor((this.page + 1) / 3) * 3;
                this.pageIndex = this.pageIndex == this.options.numberOfPages - 1 ? 0 : this.pageIndex + 1;

                pageFlip = this.currentMasterPage + 1;
                pageFlip = pageFlip > 2 ? 0 : pageFlip;
                this.masterPages[pageFlip].style.left = this.page * 100 + 100 + '%';

                pageFlipIndex = this.page + 1;
            }

            // Add active class to current page
            className = this.masterPages[this.currentMasterPage].className;
            /(^|\s)swipeview-active(\s|$)/.test(className) || (this.masterPages[this.currentMasterPage].className = !className ? 'swipeview-active' : className + ' swipeview-active');

            // Add loading class to flipped page
            className = this.masterPages[pageFlip].className;
            /(^|\s)swipeview-loading(\s|$)/.test(className) || (this.masterPages[pageFlip].className = !className ? 'swipeview-loading' : className + ' swipeview-loading');

            pageFlipIndex = pageFlipIndex - Math.floor(pageFlipIndex / this.options.numberOfPages) * this.options.numberOfPages;
            this.masterPages[pageFlip].dataset.upcomingPageIndex = pageFlipIndex;        // Index to be loaded in the newly flipped page

            newX = -this.page * this.pageWidth;
            
            if ( vendor )
                this.slider.style[vendor.transitionDuration] = Math.floor(500 * Math.abs(this.x - newX) / this.pageWidth) + 'ms';

            // Hide the next page if we decided to disable looping
            if (!this.options.loop) {
                this.masterPages[pageFlip].style.visibility = newX === 0 || newX == this.maxX ? 'hidden' : '';
            }

            if (this.x == newX) {
                this.__flip();        // If we swiped all the way long to the next page (extremely rare but still)
            } else {
                this.__pos(newX);
                if (this.options.hastyPageFlip) this.__flip();
            }
        },

        __flip: function () {
            this.__event('flip');

            for (var i=0; i<3; i++) {
                this.masterPages[i].className = this.masterPages[i].className.replace(/(^|\s)swipeview-loading(\s|$)/, '');        // Remove the loading class
                this.masterPages[i].dataset.pageIndex = this.masterPages[i].dataset.upcomingPageIndex;
            }
        },

        __event: function (type) {
            var ev = document.createEvent("Event");

            ev.initEvent('swipeview-' + type, true, true);

            this.wrapper.dispatchEvent(ev);
        }
    };

    return SwipeView;
})();
