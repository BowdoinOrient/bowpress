var ctx = document.getElementById("happy-betterworse-world-damn").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

// 64.1% think the world will be a better place in 25 years, up from 55% last semester
// 60.2% said they are happy, down from 82.1% last semester
// 73.6% said they give a damn, up from 69.9% last semester
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Are you happy?", "World better in 25 years?", "Do you give a damn?"],
    datasets: [{
      label: 'Last Semester',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [82.1, 55, 69.9],
    }, {
      label: 'This Semester',
      backgroundColor: "#48639C",
      stack: 'Stack 1',
      data: [60.2, 64.1, 73.6],
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

