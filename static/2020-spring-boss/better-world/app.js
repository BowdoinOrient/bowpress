var ctx = document.getElementById("better-world").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Will the world be better in 25 years?"],
    datasets: [{
      label: 'Last Semester',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [55],
    }, {
      label: 'This Semester',
      backgroundColor: "#48639C",
      stack: 'Stack 1',
      data: [64.1],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 80,
            },  
        }]
    }
}
});