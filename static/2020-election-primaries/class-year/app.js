var ctx = document.getElementById("class-year").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["2023", "2022", "2021", "2020 or older"],
    datasets: [
    {
      label: 'Voters',
      backgroundColor: "#8974A5", 
      data: [152, 140, 107, 162]
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 100,
               max: 175,
            },  
            scaleLabel: {
               display: true,
               labelString: "Number of votes"
            }
        }]
    }
}
});

