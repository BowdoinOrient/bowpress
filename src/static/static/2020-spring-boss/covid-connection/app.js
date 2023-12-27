var ctx = document.getElementById("covid-connection").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Immediate family", "Extend family", "Friend", "Other", "Myself", "Neighbor", "No connection"],
    datasets: [{
      label: 'Percent',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [2.8, 11.7, 27.9, 4.9, 0.7, 8, 44],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 50,
            },  
        }]
    }
}
});