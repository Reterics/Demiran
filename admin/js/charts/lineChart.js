const drawLineChart = function (options) {
    if (!options || typeof options !== "object" || !options.selector) {
        return null;
    }
    const node = document.querySelector(options.selector);
    if (!node) {
        console.error("Invalid selector");
        return null;
    }
    if (!Array.isArray(options.data)) {
        console.error("Invalid input data");
        return null;
    }
    const dateKey = options.date ? options.date : "date";
    const valueKey = options.value ? options.value : "value";
    const colorKey = options.color ? options.color : "color";

    const inputData = options.data.sort((a, b) => b[dateKey] - a[dateKey]);

    const {width, height} = node.getBoundingClientRect();

    const margin = {top: 20, right: 60, bottom: 25, left: 25};

    let innerWidth =     width - margin.left - margin.right;
    let innerHeight =    height - margin.top - margin.bottom;

    const svg = d3.select(node).append("svg")
        .attr("width",  width)
        .attr("height", height)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    const x = d3.scaleTime().range([0, innerWidth]);
    x.domain([inputData[0][dateKey], inputData[inputData.length-1][dateKey]]);
    console.log("Domain: ", [inputData[0][dateKey], inputData[inputData.length-1][dateKey]]);
    console.log("Width: ", innerWidth);

    const y = d3.scaleLinear().range([innerHeight, 0]);

    y.domain(d3.extent(inputData, function(d) { return d[valueKey]; }));

    const valueline = d3.line()
        .x(function(d) {    return x(d[dateKey]); })
        .y(function(d) { return y(d[valueKey]);  })

        .curve(d3.curveMonotoneX);

    svg.append("path")
        .data([inputData])
        .attr("class", "line")
        .attr("stroke", "steelblue")
        .attr("stroke-width", "3")
        .attr("fill", "none")
        .attr("shape-rendering", "crispEdges")
        .attr("d", valueline);


    const xAxis_woy = d3.axisBottom(x)
        //.tickFormat(d3.timeFormat("Week %V"))
        .tickValues(inputData.map(d=>d[[dateKey]]));

    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + innerHeight + ")")
        .call(xAxis_woy);

    /*svg.selectAll(".dot")
        .data(inputData)
        .enter()
        .append("circle") // Uses the enter().append() method
        .attr("class", "dot") // Assign a class for styling
        .attr("cx", function(d) { return x(d[[dateKey]]) })
        .attr("cy", function(d) { return y(d[valueKey]) })
        .attr("r", 5);
*/

    svg.selectAll(".text")
        .data(inputData)
        .enter()
        .append("text") // Uses the enter().append() method
        .attr("class", "label") // Assign a class for styling
        .attr("x", function(d, i) { return x(d[[dateKey]]) })
        .attr("y", function(d) { return y(d[valueKey]) })
        .attr("dy", "-5")
        .style("font-size", "13px")
        .text(function(d) {return d[valueKey]; });


};