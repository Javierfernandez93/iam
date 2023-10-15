class Loader {
  construct() {
      this.element = null;
      this.html = null;
      this.classes = null;
  }
  setElement(element)  {
      this.element = element;
  }
  getElement()  {
      return this.element;
  }
  show(element,__class)  {
      this.close();
      this.showLoader(element,__class);
  }
  existLoader()  {
      return $("#z-loader").length > 0
  }
  showLoaderMagic(element)  {
      $(element).addClass("overlay-loader-element");
  }
  showLoader(element,__class)  {
      if(this.existLoader() == false)
      {
          const _element = element != undefined ?element : $("#default-loader")
          this.setElement($(_element));
          this.html = this.getElement().html();
          this.classes = this.getElement().attr("class");
          
          if(this.getElement().length > 0)
          {
              let _class = __class != undefined ? __class : "preloader-sm";

              if(this.getElement().get(0).tagName == "BUTTON")
              {
                  this.getElement().attr("disabled",true);
                  this.getElement().css("width","auto");

                  this.getElement().removeClass("btn-block");
                  this.getElement().html("<div class='d-flex justify-content-center'><div class='"+_class+"'></div></div>");
                  this.getElement().animate({
                      width: "120px",
                  }, DELTA_TIME);
              } else if(this.getElement().get(0).tagName == "SPAN") {
                  this.getElement().html("<div class='d-flex justify-content-center'><div class='preloader-sm-black'></div></div>");
              } else if(this.getElement().get(0).tagName == "DIV") {
                  this.getElement().html('<div class="progress-mds" id="z-loader"><div class="indeterminate"></div></div>');
              }
          }
      }
  }
  removeStyle()  {
      this.getElement().removeAttr("style");
      this.getElement().html(this.html);
      this.getElement().attr("class",this.classes);
  }
  hideLoader()  {
      this.closeLoader();
  }
  close()  {
      this.closeLoader();
  }
  hide()  {
      this.closeLoader();
  }
  closeLoader()  {
      if(this.getElement())
      {
          this.getElement().removeAttr("disabled");
          this.removeStyle();
      }
  }
}

export { Loader }