const DELAY = 100;

class BoxLoader 
{
  constructor(delay)
  {
    this.text = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
    this.id = 'box-container';
    this.objectLoader = null;
    this.loaded = false;
    this.delay = delay != undefined ? delay : DELAY;
    this.options = {
      animation : true,
      animation_time : {
        in : 300,
        out : 300,
      }
    };
  }
  closeLoader(callback)
  {
    return new Promise(resolve => {
      if(this.options.animation)
      {
        this.animateOut(()=>{
          resolve();
        });
      } else {
        resolve();
      }
    });
  }
  animateOut(callback){
    if(this.objectLoader != null)
    {
      $(this.objectLoader).animate({'opacity':'0'},this.options.animation_time.out,()=>{
        $(this.objectLoader).addClass("d-none");

        setTimeout(()=>{
          if(callback != undefined) callback();
        },500);
      });
    }

    if(callback != undefined) callback();
  }
  animateIn(callback){
    if(this.objectLoader != null)
    {
      $(this).removeClass("d-none");

      $(this.objectLoader).animate({'opacity':'1'},this.options.animation_time.in,()=>{
        if(callback != undefined) callback();
      });
    }

    if(callback != undefined) callback();
  }
  setLoader(callback,loader)
  {
    return new Promise(resolve => {
      if(loader == true)
      {
        this.objectLoader = document.getElementById(this.id);
        
        if(this.options.animation) {
          this.animateIn();
        }

        this.objectLoader.innerHTML = this.text;
      }

      this.loaded = true;
      
      setTimeout(()=> resolve() ,this.delay);
    });
  }
};