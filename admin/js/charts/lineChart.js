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

    const inputData = options.data.sort((a, b) => a[dateKey] - b[dateKey]);

    const {width, height} = node.getBoundingClientRect();

    const margin = {top: 20, right: 60, bottom: 25, left: 60};

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

    const yAxis = d3.axisLeft(y)
        .ticks(8);
    svg.append("g")
        .call(yAxis);

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


    const bisect = d3.bisector(function(d) { return d[dateKey]; }).left;

    const focus = svg
        .append('g')
        .append('circle')
        .style("fill", "none")
        .attr("stroke", "black")
        .attr('r', 8.5)
        .style("opacity", 0)

    let selectedData;

    const svgPosition = svg.node().getBoundingClientRect();

    function mousemove() {
        const x0 = x.invert(d3.mouse(this)[0]);
        const i = bisect(inputData, x0);//, 1
        selectedData = inputData[i];

        if(selectedData){
            focus
                .attr("cx", x(selectedData[dateKey]))
                .attr("cy", y(selectedData[valueKey]))

            const html = "Időpont: "+(selectedData[dateKey].toISOString().replace("T", " ").split(".")[0])+
                "<br> Bevétel: " + selectedData[valueKey] + " Ft";
            Demiran.tooltip.html(html);
            Demiran.tooltip.show({
                x:svgPosition.left + x(selectedData[dateKey]),
                y:svgPosition.top + y(selectedData[valueKey]),
            });
        }

    }

    function mouseover(){
        focus.style("opacity", 1)
    }
    function mouseout() {
        focus.style("opacity", 0)
        Demiran.tooltip.hide();
    }
    svg
        .append('rect')
        .style("fill", "none")
        .style("pointer-events", "all")
        .attr('width', innerWidth)
        .attr('height', innerHeight)
        .on('mouseover', mouseover)
        .on('mousemove', mousemove)
        .on('mouseout', mouseout);
};