const drawBarChart = function (options) {
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
    node.innerHTML = "";
    const nameKey = options.name ? options.name : "name";
    const valueKey = options.value ? options.value : "value";
    const colorKey = options.color ? options.color : "color";

    const inputData = options.data.sort((a, b) => b[valueKey] - a[valueKey]);


    const {width, height} = node.getBoundingClientRect();

    const margin = {top: 30, right: 30, bottom: 70, left: 60},
        innerWidth = width - margin.left - margin.right,
        innerHeight = height - margin.top - margin.bottom;

    const svg = d3.select(node)
        .append("svg")
        .attr("width", innerWidth + margin.left + margin.right)
        .attr("height", innerHeight + margin.top + margin.bottom)
        .append("g")
        .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");


    const x = d3.scaleBand()
        .range([0, innerWidth])
        .domain(inputData.map(function (d) {
            return d[nameKey];
        }))
        .padding(0.2);
    svg.append("g")
        .attr("transform", "translate(0," + innerHeight + ")")
        .call(d3.axisBottom(x))
        .selectAll("text")
        .attr("transform", "translate(-10,0)rotate(-45)")
        .style("font-size", "13px")
        .style("text-anchor", "end");


    const y = d3.scaleLinear()
        .domain(d3.extent(inputData, function(d) { return d[valueKey]; }))
        .range([innerHeight, 0]);
    svg.append("g")
        .call(d3.axisLeft(y));

    svg.selectAll("bar")
        .data(inputData)
        .enter()
        .append("rect")
        .attr("x", function (d) {
            return x(d[nameKey]);
        })
        .attr("y", function (d) {
            return y(d[valueKey]);
        })
        .attr("width", x.bandwidth())
        .attr("height", function (d) {
            return innerHeight - y(d[valueKey]);
        })
        .attr("fill", function (d) {
            return d[colorKey];
        });
    return svg.node();
};