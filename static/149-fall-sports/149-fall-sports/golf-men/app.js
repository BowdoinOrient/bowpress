var ctx = document.getElementById("golf-men").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Inv.","Bobcat Inv.", "Williams Inv.",	"NESCAC Fall Qualifier", "CBB Champs."],
    datasets: [ {
      label: 'Men',
      backgroundColor: "#8974A5",
      data: [103, 64, 78, 69, 39],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 30,
               max: 105,
            },  
        }]
    }
}
});

