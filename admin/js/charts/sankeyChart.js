//#Forked from https://bl.ocks.org/GerardoFurtado/ff2096ed1aa29bb74fa151a39e9c1387
// Authors: d3noob and GerardoFurtado
// Date: July 23, 2018
// Modified: Attila Reterics
// Date: November 19, 2019
const drawSankeyChart = function (options) {
    if (!options || typeof options !== "object" || !options.selector) {
        return null;
    }
    const graphNode = document.querySelector(options.selector);
    if (!graphNode) {
        console.error("Invalid selector");
        return null;
    }
    const data = options.data || {};

    graphNode.style.height = '100%';
    graphNode.style.width = '100%';
    graphNode.innerHTML = '';

    if (!graphNode) {
        throw Error('There is no DOM element for receiving graph');
    }

    const verticalSankey = options.type === 'vertical';
    if (verticalSankey && typeof d3.verticalSankey !== 'function') {
        console.error("There is no vertical Sankey implementation");
        return;
    }
    if (!verticalSankey && typeof d3.sankey !== 'function') {
        console.error("There is no Sankey implementation");
        return;
    }
    if (data && Array.isArray(data.nodes)) {
        data.nodes.forEach((node, index) => {
            if (typeof node.node !== 'number') {
                node.node = index;
            }
        });
        data.links.forEach((link) => {
            link.value = 2;
        });
    }
    const fillKey = options.fillKey || 'fillKey';
    const fills = options.fills || ["steelblue","steelblue","steelblue","steelblue"];

    const margin = options.margin || {
        top: typeof options.marginTop === 'number' ? options.marginTop : 10,
        right: typeof options.marginRight === 'number' ? options.marginRight : 10,
        bottom:
            typeof options.marginBottom === 'number' ? options.marginBottom : 10,
        left: typeof options.marginLeft === 'number' ? options.marginLeft : 10,
    };

    const outerWidth = options.width || graphNode.offsetWidth;
    const outerHeight = options.height || graphNode.offsetHeight;

    const units = "Widgets";

// set the dimensions and margins of the graph
    const width = outerWidth - margin.left - margin.right;
    const height = outerHeight - margin.top - margin.bottom;

// format variables
    const formatNumber = d3.format(",.0f"),    // zero decimal places
        format = function(d) { return formatNumber(d) + " " + units; },
        color = d3.scaleOrdinal(d3.schemeCategory10);

// append the svg object to the body of the page
    const svg = d3.select(graphNode).append("svg")
        .attr("width", outerWidth)
        .attr("height", outerHeight)
        .append("g")
        .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");

// Set the sankey diagram properties
    const sankey = (!verticalSankey ? d3.sankey() : d3.verticalSankey())
        .nodeWidth(140)
        .nodePadding(10)
        .size([width, height]);

    const path = sankey.link();

    sankey
        .nodes(data.nodes)
        .links(data.links)
        .layout(32);
    const nodeWidth = sankey.nodeWidth();

    const link = svg.append("g").selectAll(".link")
        .data(data.links)
        .enter().append("path")
        .attr("class", "link")
        .attr("d", path)
        .style("fill","none")
        .style("stroke","#000")
        .style("stroke-opacity",0.2)
        .style("stroke-width", function(d) { return Math.max(1, d.dy); })
        .sort(function(a, b) { return b.dy - a.dy; });

    link.append("title")
        .text(function(d) {
            return d.source.name + " → " +
                d.target.name + "\n" });

    const sankeyNode = svg.append("g").selectAll(".node")
        .data(data.nodes)
        .enter().append("g")
        .attr("class", "node")
        .attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")"; })
        .call(d3.drag()
            .subject(function(d) {
                return d;
            })
            .on("start", function() {
                this.parentNode.appendChild(this);
            })
            .on("drag", dragmove));

    sankeyNode.append("rect")
        .attr("height", verticalSankey ? nodeWidth : function(d) { return d.dy; })
        .attr("width", verticalSankey ?  function(d) { return d.dy; } : nodeWidth)
        .style("fill", function(d) {
            if (Array.isArray(d[fillKey])) {
                const max = d3.max(d[fillKey]);
                return fills[max];
            } else {
                return fills[d[fillKey]]
            }
        })
        .style("stroke", function(d) {
            if (Array.isArray(d[fillKey])) {
                const max = d3.max(d[fillKey]);
                return fills[max];
            } else {
                return fills[d[fillKey]]
            }
        })
        .style("cursor","move")
        .style("fill-opacity",.9)
        .style("shape-rendering","crispEdges")
        .append("title")
        .text(function(d) {
            return d.name });

    sankeyNode.append("text")
        .attr("x", verticalSankey ? function(d) { return d.dy / 2; } : nodeWidth/2)
        .attr("y", verticalSankey ? nodeWidth/2 : function(d) { return d.dy / 2; })
        .attr("dy", ".35em")
        .attr("text-anchor", "middle")
        .attr("transform", null)
        .style("pointer-events","none")
        .text(function(d) { return d.name; });
    /*.filter(function(d) { return d.x < width / 2; })
    .attr("x", 6 + sankey.nodeWidth())
    .attr("text-anchor", "start");*/

    function dragmove(d) {
        if (verticalSankey) {
            d3.select(this).attr("transform", "translate(" + (d.x = Math.max(0, Math.min(width - d.dy, d3.event.x))) + "," + d.y + ")");

        } else {
            d3.select(this)
                .attr("transform",
                    "translate("
                    + d.x + ","
                    + (d.y = Math.max(
                        0, Math.min(height - d.dy, d3.event.y))
                    ) + ")");
        }
        sankey.relayout();
        link.attr("d", path);
    }

};

