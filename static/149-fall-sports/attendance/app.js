var ctx = document.getElementById("attendance").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Men Soccer", "Women Soccer","Volleyball", "Field Hockey",	"Football"],
    datasets: [{
      label: 'Number in Attendance',
      backgroundColor: "#CAB1D1", 
      data: [183.3,116.6,123,139.9,3258.3],
    }]
  },
  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 3300,
            },  
            scaleLabel: {
               display: true,
               labelString: "People"
            }
        }]
    } 
}
});