const drawPieChart = function (options) {
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
    const nameKey = options.name ? options.name : "name";
    const valueKey = options.value ? options.value : "value";
    const colorKey = options.color ? options.color : "color";

    const {width, height} = node.getBoundingClientRect();
    const radius = Math.min(width, height) / 2;

    const data = options.data || [];

    const totals = d3.sum(data, function(d) {
        return d[valueKey];
    });
    data.forEach(function(d) {
        d._percentage = Math.round(d[valueKey]  / totals * 100);
    });

    const svg = d3.select(node)
        .append("svg")
        .attr("width", width)
        .attr("height", height)
        .append("g")
        .attr("transform", `translate(${width / 2}, ${height / 2})`);

    const color = d3.scaleOrdinal(["#66c2a5","#fc8d62","#8da0cb",
        "#e78ac3","#a6d854","#ffd92f"]);

    const pie = d3.pie()
        .value(d => d[valueKey])
        .sort(null);

    const arc = d3.arc()
        .innerRadius(0)
        .outerRadius(radius * 0.85);

    const arcOuter = d3.arc()
        .innerRadius(0)
        .outerRadius(radius);


    function arcTween(a) {
        const i = d3.interpolate(this._current, a);
        this._current = i(1);
        return (t) => arc(i(t));
    }
    const key = function(d){ return d.data[nameKey]; };
    const svgPosition = svg.node().getBoundingClientRect();

    function update(val = this.value) {
        // Join new data
        //console.log(val);
        const path = svg.selectAll("path")
            .data(pie(data));

        // Update existing arcs
        path.transition().duration(200).attrTween("d", arcTween);

        // Enter new arcs
        path.enter().append("path")
            .attr("fill", (d, i) => color(i))
            .attr("d", arc)
            .attr("stroke", "white")
            .attr("stroke-width", "6px")
            .each(function(d) { this._current = d; })
            .on("mouseover", function(d) {
                d3.select(this)
                    .transition()
                    .attr("d", arcOuter)
                    .duration(300);
                Demiran.tooltip.html("Kategória: " + d.data.name +
                "<br>Darabszám: " + d.data.price +
                "<br>Arány: " + d.data._percentage);
                Demiran.tooltip.show(d3.event)

            })
            .on("mousemove", function (){
                Demiran.tooltip.show(d3.event);
            })
            .on("mouseout", function(d) {
                d3.select(this)
                    .transition()
                    .attr("d", arc)
                    .duration(300);
                Demiran.tooltip.hide();
            });



        const text = svg.selectAll("text")
            .data(pie(data));

        text.enter().append("text")
            .attr("transform", function (d) {
                return "translate(" + arc.centroid(d) + ")";
            })
            .attr("text-anchor", "middle")
            .attr("cursor", "pointer")
            .attr("fill", "white")
            .text(function (d) {
                return d.data[nameKey];
            }).on("mousemove", function (){
                Demiran.tooltip.show(d3.event);
            });



    }

    update(nameKey);

}