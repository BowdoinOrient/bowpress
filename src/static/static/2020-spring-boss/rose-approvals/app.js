var ctx = document.getElementById("rose-approvals").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Fall 2018", "Spring 2019", "Fall 2019", "Spring 2020"],
    datasets: [{
      label: 'Disapprove',
      backgroundColor: "#CAB1D1",
      stack: 'Stack 0',
      data: [-3.6, -9.6, -14.9, -10.2],
    }, {
      label: 'Strongly Disapprove',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [-1.1, -2.2, -5.3, -3.3],
    }, {
      label: 'Approve',
      backgroundColor: "#48639C",
      stack: 'Stack 0',
      data: [45.6, 39.5, 32.5, 46.9],
    }, {
      label: 'Strongly Approve',
      backgroundColor: "#1D3461",
      stack: 'Stack 0',
      data: [28.5, 20.3, 9.9, 13.5],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: -30,
               max: 80,
            },  
        }]
    }
}
});


