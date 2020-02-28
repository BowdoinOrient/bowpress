var ctx = document.getElementById("points-swim").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

// Men’s: MIT-62; WPI-144; Bates-142; Wesleyan-171; Trinity-161; Colby-93
// Women’s: MIT-113; WPI-220; Bates-173; Wesleyan-195; Trinity-203; Colby-177.5

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["MIT", "WPI",	"Bates",	"Wesleyan",	"Trinity", "Colby"],
    datasets: [{
      label: 'Men',
      backgroundColor: "#CAB1D1", 
      data: [62, 144, 142, 171, 161, 93]
    },
    {
      label: 'Women',
      backgroundColor: "#8974A5", 
      data: [113, 220, 173, 195, 203, 177.5]
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 50,
               max: 250,
            },  
            scaleLabel: {
               display: true,
               labelString: "Points (in scoring meets)"
            }
        }]
    }
}
});

