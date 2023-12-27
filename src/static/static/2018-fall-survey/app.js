var w = 650;
var h = 1200;
var margin = { top: 20, right: 10, bottom: 50, left: 10 };

var svg = d3
  .select("#big-graph")

d3.csv("/static/2018-fall-survey/data.csv").then(function(dataset) {
  var series = d3
    .stack()
    .keys([
      "neg_disapprove",
      "neg_strong_disapprove",
      "approve",
      "strong_approve"
    ])
    .offset(d3.stackOffsetDiverging)(dataset);

  var yScale = d3
    .scaleBand()
    .domain(
      dataset.map(function(d) {
        return d.institution;
      })
    )
    .rangeRound([margin.top, h - margin.bottom])
    .padding(0.1);

  var xScale = d3
    .scaleLinear()
    .domain([d3.min(series, stackMin), d3.max(series, stackMax)])
    .rangeRound([margin.left, w - margin.right]);

  var z = d3.scaleOrdinal(["#C45D5B", "#601F1A", "#6587B7", "#1B293F"]);

  svg
    .append("g")
    .selectAll("g")
    .data(series)
    .enter()
    .append("g")
    .attr("fill", function(d) {
      return z(d.key);
    })
    .selectAll("rect")
    .data(function(d) {
      return d;
    })
    .enter()
    .append("rect")
    .attr("x", function(d) {
      return xScale(d[0]);
    })
    .attr("y", function(d) {
      return yScale(d.data.institution);
    })
    .attr("height", function(d) {
      return 35;
    })
    .attr("width", function(d) {
      return xScale(d[1]) - xScale(d[0]);
    })
    .on("mouseover", function(d) {
      //Get this bar's x/y values, then augment for the tooltip
      var xPosition = d3.event.pageX;
      var yPosition = d3.event.pageY;

      //Update the tooltip position and value
      d3.select("#tooltip")
        .style("left", xPosition + "px")
        .style("top", yPosition + "px")
        .select("#value")
        .text(Math.abs(xScale(d[1]) - xScale(d[0])));

      //Show the tooltip
      d3.select("#tooltip").classed("hidden", false);
    })
    .on("mouseout", function() {
      //Hide the tooltip
      d3.select("#tooltip").classed("hidden", true);
    });

  svg
    .append("g")
    .attr("transform", "translate(" + xScale(0) + ", 0)")
    .style("font", "15px sans-serif")
    .style("color", "white")
    .style("stroke", "black")
    .style("paint-order", "stroke")
    .style("stroke-width", "3px")
    .style("pointer-events", "none")
    .call(d3.axisRight(yScale));

  svg
    .append("g")
    .attr("transform", "translate(0, " + margin.top + ")")
    .style("font", "12px sans-serif")
    .call(d3.axisTop(xScale)
      .tickValues([-115, -58, 0, 58, 115, 173, 231, 289, 347, 405])
      .tickFormat(function(d) { return parseInt(d/575*100) + "%"; })
    );

  svg
    .append("g")
    .attr("transform", "translate(0, " + (h - margin.bottom) + ")")
    .style("font", "12px sans-serif")
    .call(d3.axisBottom(xScale)
      .tickValues([-115, -58, 0, 58, 115, 173, 231, 289, 347, 405])
      .tickFormat(function(d) { return parseInt(d/575*100) + "%"; })
    );

  // svg.append("")
});

function stackMin(serie) {
  return d3.min(serie, function(d) {
    return d[0];
  });
}

function stackMax(serie) {
  return d3.max(serie, function(d) {
    return d[1];
  });
}

