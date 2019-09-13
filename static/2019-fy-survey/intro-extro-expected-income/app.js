var ctx = document.getElementById("introExtroExpectedIncome").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Less than $30,000", "$30,000-$50,000", "$50,000-$70,000",	"$70,000-$90,000",	"More than $90,000"],
    datasets: [{
      label: 'Extrovert',
      backgroundColor: "#CAB1D1",
      data: [7.9,	27.0,	34.8,	15.7, 14.6,],
    }, {
      label: 'Introvert',
      backgroundColor: "#8974A5",
      data: [10.9, 35.3, 37.0,	10.9,	5.9,],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 50,
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
