var ctx = document.getElementById("golf-women").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Inv.", "Polar Bear Shootout",	"Bobcat Inv.", "Mt. Holyoke Inv.", "Williams Inv.", "George Phinney Classic",	"NESCAC Fall Qualifier", "CBB Champs."],
    datasets: [{
      label: 'Women',
      backgroundColor: "#CAB1D1",
      data: [44, 57, 93, 102,	0, 213, 190, 0],
    }, {
      label: 'Men',
      backgroundColor: "#8974A5",
      data: [103,	0, 64, 0, 78, 0, 69, 39],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 175,
            },  
        }]
    }
}
});

