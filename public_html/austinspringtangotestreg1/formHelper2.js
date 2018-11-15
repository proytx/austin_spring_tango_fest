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
    //if(!fh.classes){alert('No fh.classes');return;} 
	
	fh.package1=document.getElementById('package1');
	fh.package2=document.getElementById('package2');
	fh.package3=document.getElementById('package3');
	fh.package4=document.getElementById('package4');
	
	if (document.tango1) fh.chex = eval(document.tango1["classes[]"]);
	else if (document.ASTF2) fh.chex = eval(document.ASTF2["classes[]"]);
	if (document.tango1) fh.milongas = eval(document.tango1["milongas[]"]);
	else if (document.ASTF2) fh.milongas = eval(document.ASTF2["milongas[]"]);

    //DOMhelp.cssjs('add',fh.classes,fh.hidingClass);
//	DOMhelp.addEvent(fh.package1,'click',fh.unhide,false);
//	DOMhelp.addEvent(fh.package2,'click',fh.unhide,false);
//	DOMhelp.addEvent(fh.package3,'click',fh.unhide,false);
	//DOMhelp.addEvent(fh.package4,'click',fh.unhide,false);
	
//	DOMhelp.addEvent(fh.package1,'click',fh.p1on,false);
//	DOMhelp.addEvent(fh.package2,'click',fh.p2on,false);
//	DOMhelp.addEvent(fh.package3,'click',fh.p3on,false);
	//DOMhelp.addEvent(fh.package4,'click',fh.p4on,false);
	},
	
	p1on:function(){
		fh.checker(fh.chex,1,[]);
		fh.checker(fh.milongas,1,[]);
	},

	p2on:function(){
		fh.checker(fh.chex,0,[]);
		fh.checker(fh.milongas,1,[]);
	},

	p3on:function(){
		<!-- Boris: example showing how to select just 1 class -->
		fh.checker(fh.chex,0,[]);
		fh.checker(fh.milongas,0,[]);
		//fh.checker(fh.chex,1,['class6']);  
	},

	p4on:function(){
		fh.checker(fh.chex,0,[]);
		fh.checker(fh.milongas,0,[]);
	},
	  
	checker:function(chex,action,exclude){
	  for(var i=0;i<chex.length;i++){
		if(chex[i].type!='checkbox' || exclude.contains(chex[i].getAttribute('id'))){chex[i].checked=!action;continue;}
		chex[i].checked=action;
		<!-- Partha: added disable action otherwise one user in 2012 manually deselected classes after selecting a package removed when it created havoc-->
		//chex[i].disabled=(action==1); 
	  }
	},
   
  unhide:function(){
    if(DOMhelp.cssjs('check',fh.classes,fh.hidingClass)){
       DOMhelp.cssjs('remove',fh.classes,fh.hidingClass);}	
  }
}

DOMhelp.addEvent(window,'load',fh.init,false);
