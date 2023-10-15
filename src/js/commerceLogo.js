html2canvas(document.body, {
  onrendered: function(canvas) {
    document.body.appendChild(canvas);
  },
  width: 300,
  height: 300
});