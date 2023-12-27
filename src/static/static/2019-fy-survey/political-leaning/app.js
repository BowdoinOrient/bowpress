var ctx = document.getElementById("politicalLeaning").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["1 (Very Liberal)", "2", "3", "4", "5 (Very Conservative)"],
    datasets: [{
      label: 'Class of 2023',
      backgroundColor: "#f1c40f",
      data: [27.1, 40.5, 25.7, 6.2, 0.5],
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
