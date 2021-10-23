const drawLineChart = function (options) {
    if (!options || typeof options !== "object" || !options.selector) {
        return null;
    }
    const node = document.querySelector(options.selector);
    if (!node) {
        console.error("Invalid selector");
        return null;
    }
    node.innerHTML = "";
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


    function multiFormat(date) {
        const formatMillisecond = d3.timeFormat(".%L"),
            formatSecond = d3.timeFormat(":%S"),
            formatMinute = d3.timeFormat("%I:%M"),
            formatHour = d3.timeFormat("%I:%M %p"),
            formatDay = d3.timeFormat("%a %d"),
            formatWeek = d3.timeFormat("%b %d"),
            formatMonth = d3.timeFormat("%B"),
            formatYear = d3.timeFormat("%Y");

        return (d3.timeSecond(date) < date ? formatMillisecond
            : d3.timeMinute(date) < date ? formatSecond
                : d3.timeHour(date) < date ? formatMinute
                    : d3.timeDay(date) < date ? formatHour
                        : d3.timeMonth(date) < date ? (d3.timeWeek(date) < date ? formatDay : formatWeek)
                            : d3.timeYear(date) < date ? formatMonth
                                : formatYear)(date);
    }

    const xAxis_woy = d3.axisBottom(x)
        //.tickFormat(d3.timeFormat("Week %V"))
        .ticks(8)
        .tickFormat(multiFormat)
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
    const showPrice = function(d) {
        if(d[valueKey]){
            const string = d[valueKey].toString();
            return string.substr(0,3) + " " + string.substr(3,3) + " Ft ";

        } else {
            return d[valueKey];
        }
    };

    svg.selectAll(".text")
        .data(inputData)
        .enter()
        .append("text") // Uses the enter().append() method
        .attr("class", "label") // Assign a class for styling
        .attr("x", function(d, i) { return x(d[[dateKey]]) })
        .attr("y", function(d) { return y(d[valueKey]) })
        .attr("dy", "-5")
        .attr("title", showPrice)
        .style("font-size", "13px")
        .text(showPrice);

// This allows to find the closest X index of the mouse:
    var bisect = d3.bisector(function(d) { return d.x; }).left;

    // Create the circle that travels along the curve of chart
    var focus = svg
        .append('g')
        .append('circle')
        .style("fill", "none")
        .attr("stroke", "black")
        .attr('r', 8.5)
        .style("opacity", 0)

    // Create the text that travels along the curve of chart
    var focusText = svg
        .append('g')
        .append('text')
        .style("opacity", 0)
        .attr("text-anchor", "left")
        .attr("alignment-baseline", "middle");
    // What happens when the mouse move -> show the annotations at the right positions.
    function mouseover() {
        focus.style("opacity", 1)
        focusText.style("opacity",1)
    }

    function mousemove() {
        // recover coordinate we need
        var x0 = x.invert(d3.mouse(this)[0]);
        var i = bisect(data, x0, 1);
        selectedData = data[i]
        focus
            .attr("cx", x(selectedData.x))
            .attr("cy", y(selectedData.y))
        focusText
            .html("x:" + selectedData.x + "  -  " + "y:" + selectedData.y)
            .attr("x", x(selectedData.x)+15)
            .attr("y", y(selectedData.y))
    }
    function mouseout() {
        focus.style("opacity", 0)
        focusText.style("opacity", 0)
    }
    svg
        .append('rect')
        .style("fill", "none")
        .style("pointer-events", "all")
        .attr('width', width)
        .attr('height', height)
        .on('mouseover', mouseover)
        .on('mousemove', mousemove)
        .on('mouseout', mouseout);
};