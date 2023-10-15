$(document).ready(function(){
  let slider = new Slider(6000);
  slider.start();
});

class Slider {
  constructor(time,index) {
    this.time = time == undefined ? 1000 : time; // miliseconds
    this.index = index == undefined ? 0 : index; // miliseconds
    this.element_size = 0;
  }
  hideElement(index) {
    $(".box-block-bg").eq(index).css({"position":"absolute","z-index":10})

    $(".box-block-bg").eq(index).animate({
      opacity: .6,
      left: "+=1000",
      position:"absolute",
      backgroundColor:"#4E1402",
      boxShadow: "0px 0px 20px rgba(0,0,0,0.8)"
    }, 2600, function() {
      $(this).addClass("hide");
      $(this).css({"position":"relative","opacity":1,"left":0});
    });
    // $(".box-block-bg").eq(index).addClass("hide");
  }
  showElement(index) {
    $(".box-block-bg").eq(index).removeClass("hide");
    $(".box-block-bg").eq(index).css({"position":"absolute","z-index":9})
  }
  getElementsSize() {
    this.element_size = $(".box-block-bg").length;
  }
  increaseIndex() {
    if(this.index < (this.element_size-1))
      this.index++;
    else 
      this.index = 0;
  }
  stop() {
    clearInterval(this.interval);
  }
  start() {
    this.interval = setInterval(()=>{ 
      this.getElementsSize();
      this.hideElement(this.index);
      this.increaseIndex();
      this.showElement(this.index);
    },this.time);

    // $(".box-block-bg").index(a)
    // alert(this.time)
    // alert(this.index)
  }
}