var ctx = document.getElementById("substanceUse").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Drinking Alchohol", "Marijuana", "Cigarettes", "Vaping", "Other"],
    datasets: [{
      label: 'Class of 2022',
      backgroundColor: "#CAB1D1",
      data: ["71.7", "38.6",	"27.0",	"25.3",	"6.1"],
    }, {
      label: 'Class of 2023',
      backgroundColor: "#8974A5",
      data: [74.3,	43.3,	10.0,	28.6,	4.8],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 100,
               callback: function(value){return value+ "%"}
            },  
            scaleLabel: {
               display: true,
               labelString: "Percentage"
            }
        }]
    }
}
});
