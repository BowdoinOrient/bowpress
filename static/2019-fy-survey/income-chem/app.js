var ctx = document.getElementById("incomeChem").getContext('2d');

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
      label: 'Not Chem-Free',
      backgroundColor: "#CAB1D1",
      data: [82.9,	72.2,	70.4,	75.6,	83.3,	90.2,],
    }, {
      label: 'Chem-Free',
      backgroundColor: "#8974A5",
      data: [17.1,	27.8,	29.6,	24.4,	16.7,	9.8,],
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
