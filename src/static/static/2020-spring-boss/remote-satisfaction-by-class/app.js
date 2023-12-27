var ctx = document.getElementById("remote-satisfaction-by-class").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

// Seniors are most satisfied with remote class (37.8%), 
// Juniors the least (16.8%), Sophomores (28.2%), Freshman (25.2%)
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Class of 2023", "Class of 2022", "Class of 2021", "Class of 2020"],
    datasets: [{
      label: 'Satisfied',
      backgroundColor: "#48639C",
      stack: 'Stack 0',
      data: [25.2, 28.2, 16.8, 37.8],
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

