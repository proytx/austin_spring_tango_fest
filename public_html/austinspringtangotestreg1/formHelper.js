// JavaScript Document
		Array.prototype.contains = function (element)
		  {
				  for (var i = 0; i < this.length; i++)
			   {
					  if (this[i] == element)
				  {
							  return true;
					  }
				  }
				  return false;
		  };

fh={
  // CSS classes
  hidingClass:'hide', // hide elements

  init:function(){
    if(!document.getElementById || !document.createElement){return;}
	
    fh.classes=document.getElementById('indivClasses');
    if(!fh.classes){alert('No fh.classes');return;} 
	
	fh.package1=document.getElementById('package1');
	fh.package2=document.getElementById('package2');
	fh.package3=document.getElementById('package3');
	
	fh.chex = eval(document.tango1["classes[]"]);
	fh.milongas = eval(document.tango1["milongas[]"]);

    DOMhelp.cssjs('add',fh.classes,fh.hidingClass);
	DOMhelp.addEvent(fh.package1,'click',fh.unhide,false);
	DOMhelp.addEvent(fh.package2,'click',fh.unhide,false);
	DOMhelp.addEvent(fh.package3,'click',fh.unhide,false);
	
	DOMhelp.addEvent(fh.package1,'click',fh.p1on,false);
	DOMhelp.addEvent(fh.package2,'click',fh.p2on,false);
	},
	
	p1on:function(){
		fh.checker(fh.chex,1,[]);
		fh.checker(fh.milongas,1,[]);
	},
	  
	p2on:function(){
		<!-- fh.checker(fh.chex,1,['class6']);  Modifyed by Bryan to update options-->
		fh.checker(fh.chex,1,[]);
		fh.checker(fh.milongas,1,[]);
	},
	  
	checker:function(chex,action,exclude){
	  for(var i=0;i<chex.length;i++){
		if(chex[i].type!='checkbox' || exclude.contains(chex[i].getAttribute('id'))){chex[i].checked=!action;continue;}
		chex[i].checked=action;
	  }
	},
   
  unhide:function(){
    if(DOMhelp.cssjs('check',fh.classes,fh.hidingClass)){
       DOMhelp.cssjs('remove',fh.classes,fh.hidingClass);}	
  }
}

DOMhelp.addEvent(window,'load',fh.init,false);