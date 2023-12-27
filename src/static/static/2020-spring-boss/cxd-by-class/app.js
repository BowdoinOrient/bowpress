var ctx = document.getElementById("cxd-by-class").getContext('2d');

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Class of 2020", "Class of 2021", "Class of 2022", "Class of 2023"],
    datasets: [{
      label: 'Disapprove',
      backgroundColor: "#CAB1D1",
      stack: 'Stack 0',
      data: [-15.4, -8.3, -10.9, -5.1],
    }, {
      label: 'Strongly Disapprove',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [-5.8, -3.9, -0, -0.6],
    }, {
      label: 'Approve',
      backgroundColor: "#48639C",
      stack: 'Stack 0',
      data: [35.2, 28.2, 37.8, 39.1],
    }, {
      label: 'Strongly Approve',
      backgroundColor: "#1D3461",
      stack: 'Stack 0',
      data: [12.8, 13.5, 14.10, 14.7],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: -30,
               max: 70,
            },  
        }]
    }
}
});