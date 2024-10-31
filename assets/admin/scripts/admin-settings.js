function themedev_featured_copyTextData(FIledid){
	var FIledidData = document.querySelector("#"+FIledid);
	if(FIledidData){
		FIledidData.select();
		document.execCommand("copy");
	}
}

function themedev_featured_show(dat){
	
	var target = dat.getAttribute('nx-target');
	if(!target){
		return '';
	}
	
	var common = dat.getAttribute('nx-target-common');
	if(common){
		var commontDiv = document.querySelectorAll(common);
		if(commontDiv){
			for(var m = 0; m < commontDiv.length; m++){
				commontDiv[m].classList.remove('nx-show-target');
			}
		}
	}
	
	var targetDiv = document.querySelectorAll(target);
	if(targetDiv){
		for(var m = 0; m < targetDiv.length; m++){
			targetDiv[m].classList.toggle('nx-show-target');
		}
	}
}

function themedev_featured_show_default(dat, vale){
	var target = dat.getAttribute('nx-target');
	if(!target){
		return '';
	}
	if(dat.value == vale){
		var targetDiv = document.querySelectorAll(target);
		if(targetDiv){
			for(var m = 0; m < targetDiv.length; m++){
				targetDiv[m].classList.toggle('nx-show-target');
			}
		}
	}else{
		var targetDiv = document.querySelectorAll(target);
		if(targetDiv){
			for(var m = 0; m < targetDiv.length; m++){
				targetDiv[m].classList.remove('nx-show-target');
			}
		}
	}
}


function themedev_featured_copy_link(idda){
	if(idda){
		var getLink = idda.getAttribute('themedev-link');
		var linkData = prompt("Copy link, then click OK.", getLink);
		if(linkData){
			document.execCommand("copy");
		}
	}
}


function themedev_style_choose(idda){
	var targetDiv = document.querySelectorAll('.style-sec-next');
	if(targetDiv){
		for(var m = 0; m < targetDiv.length; m++){
			targetDiv[m].classList.remove('style-active');
		}
	}
	
	if(idda){
		idda.parentElement.classList.add('style-active');
	}
}