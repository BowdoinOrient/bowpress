var ctx = document.getElementById("genderExpectedIncome").getContext('2d');

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
      label: 'Female',
      backgroundColor: "#CAB1D1",
      data: [38.4,	34.8,	8.9,	12.5,	5.4,],
    }, {
      label: 'Male',
      backgroundColor: "#8974A5",
      data: [23.5, 37.9, 18.4,	6.1,	14.3,],
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
