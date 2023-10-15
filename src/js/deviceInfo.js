$(document).ready(function(){
    var performance = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance || {};
    let number_of_cores = navigator.hardwareConcurrency;
    let device_memory = navigator.deviceMemory;

    // console.error(performance);

    for (var value in performance) {
      // console.log(value + "<br>");
    }

    var canvas;
    canvas = document.getElementById("glcanvas");
    var gl = canvas.getContext("experimental-webgl");

    // console.log(gl.getParameter(gl.RENDERER) + "<br>");
    // console.log(gl.getParameter(gl.VENDOR) + "<br>");
    // console.log(getUnmaskedInfo(gl).vendor + "<br>");
    // console.log(getUnmaskedInfo(gl).renderer + "<br>");


    function getUnmaskedInfo(gl) {
      var unMaskedInfo = {
        renderer: '',
        vendor: ''
      };

      var dbgRenderInfo = gl.getExtension("WEBGL_debug_renderer_info");
      if (dbgRenderInfo != null) {
        unMaskedInfo.renderer = gl.getParameter(dbgRenderInfo.UNMASKED_RENDERER_WEBGL);
        unMaskedInfo.vendor = gl.getParameter(dbgRenderInfo.UNMASKED_VENDOR_WEBGL);
      }

      return unMaskedInfo;
    }
});