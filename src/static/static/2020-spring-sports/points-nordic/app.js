var ctx = document.getElementById("points-nordic").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

// Harvard-351; UVM-304; Colby-375; Bates-347; Williams-365
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Harvard", "UVM",	"Colby",	"Bates",	"Williams"],
    datasets: [{
      label: 'Nordic',
      backgroundColor: "#CAB1D1", 
      data: [351, 304, 375, 347, 365]
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 260,
               max: 400,
            },  
            scaleLabel: {
               display: true,
               labelString: "Points per carnival, total team scores"
            }
        }]
    }
}
});

