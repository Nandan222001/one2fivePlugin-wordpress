document.addEventListener("DOMContentLoaded", function() {
  var is_product_page = one2five_vars.is_product_page;
  if(is_product_page){
    var charts = document.querySelectorAll('.chart');
    charts.forEach(function(chart) {
      var canvas = chart.querySelector('canvas');
      var rating = parseFloat(chart.getAttribute('data-rating')); // Get the rating out of 5
      var percent = (rating / 5) * 100; // Convert rating to percentage
      var context = canvas.getContext('2d');
      var centerX = canvas.width / 2;
      var centerY = canvas.height / 2;
      var radius = canvas.width / 2;
      var lineWidth = 5; // Adjust the thickness of the ring here
  
      // Clear canvas
      context.clearRect(0, 0, canvas.width, canvas.height);
  
      // Draw the outer border (hollow circle)
      context.beginPath();
      context.arc(centerX, centerY, radius - lineWidth / 2, 0, 2 * Math.PI, false); // Adjust radius for thickness
      context.lineWidth = lineWidth;
      context.strokeStyle = '#ccc'; // Border color
      context.stroke();
  
      // Draw the completed part (arc)
      var endAngle = (2 * Math.PI * percent) / 100 - Math.PI / 2;
      context.beginPath();
      context.arc(centerX, centerY, radius - lineWidth / 2, -Math.PI / 2, endAngle, false); // Adjust radius for thickness
      context.lineWidth = lineWidth; // Set arc width
      context.strokeStyle = one2five_chart_vars.accent_color; // Color for the completed part (black)
      context.stroke();
    });
  }

  });
  