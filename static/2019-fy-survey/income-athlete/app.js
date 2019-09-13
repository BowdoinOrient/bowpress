var ctx = document.getElementById("incomeAthlete").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Below $40,000", "$40,000-$80,000",	"$80,000-$125,000",	"$125,000-$250,000",	"$250,000-$500,000",	"More than $500,000"],
    datasets: [{
      label: 'Non-Athlete',
      backgroundColor: "#CAB1D1",
      data: [83.3,	96.3,	90.2,	75.9,	80.5,	55.2,],
    }, {
      label: 'Athlete',
      backgroundColor: "#8974A5",
      data: [16.7,	3.7,	9.8,	24.1,	19.5,	44.8,],
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
