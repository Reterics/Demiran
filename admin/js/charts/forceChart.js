const drawForceChart = function (options) {
    if (!options || typeof options !== "object" || !options.selector) {
        return null;
    }
    const node = document.querySelector(options.selector);
    if (!node) {
        console.error("Invalid selector");
        return null;
    }

    const {width, height} = node.getBoundingClientRect();

    let graph;
    if(options.data && options.data.links && options.data.nodes){
        graph = options.data;
    } else if(Array.isArray(options.data)){
        const nodeList = [];
        graph = {
            nodes:[],
            links:[]
        };
        for(let i = 0; i < options.data.length; i++) {
            const d = options.data[i];
            if(d){
                if(!d.value) {
                    options.data[i].value = 1;
                }
                if(d.source && d.target){
                    if(!nodeList.includes(d.source)){
                        nodeList.push(d.source);
                    }
                    if(!nodeList.includes(d.target)){
                        nodeList.push(d.target);
                    }
                    graph.links.push(d);
                }
            }
        }
        nodeList.forEach(function(node){
            graph.nodes.push({
                id: node,
                group: 1
            })
        });
    } else {
        console.error("Invalid input data");
        return "Invalid input data";
    }
    const svg = d3.select(node)
        .append("svg")
        .attr("width", width)
        .attr("height", height);

    const color = d3.scaleOrdinal(["#66c2a5","#fc8d62","#8da0cb",
        "#e78ac3","#a6d854","#ffd92f"]);

    const simulation = d3.forceSimulation()
        .force("link", d3.forceLink().id(function(d) { return d.id; }))
        .force("charge", d3.forceManyBody())
        .force("center", d3.forceCenter(width / 2, height / 2));



    const link = svg.append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(graph.links)
        .enter().append("line")
        .style("stroke", "#999")
        .style("stroke-opacity", "0.6")
        .attr("stroke-width", function(d) { return Math.sqrt(d.value); });

    const nodeCircle = svg.append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(graph.nodes)
        .enter().append("g")

    const circles = nodeCircle.append("circle")
        .attr("r", 5)
        .style("stroke", "#fff")
        .style("stroke-width", "1.5px")
        .attr("fill", function(d) { return color(d.group); });

    // Create a drag handler and append it to the node object instead
    const drag_handler = d3.drag()
        .on("start", dragstarted)
        .on("drag", dragged)
        .on("end", dragended);

    drag_handler(nodeCircle);

    const lables = nodeCircle.append("text")
        .text(function(d) {
            return d.name || d.id;
        })
        .attr('x', 6)
        .attr('y', 3);

    nodeCircle.append("title")
        .text(function(d) { return d.name || d.id; });

    simulation
        .nodes(graph.nodes)
        .on("tick", ticked);

    simulation.force("link")
        .links(graph.links);


    function ticked() {

        link
            .attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });

        nodeCircle
            .attr("transform", function(d) {
                if(d.x > width/4 && d.group === '1') {
                    d.x = width/4;
                } else if(d.x < width/4*3 && d.group === '3') {
                    d.x = width/4*3;
                }
                d.y = Math.floor(d.y / 10) * 10;
                return "translate(" + d.x + "," + d.y + ")";
            })
    }


    function dragstarted(d) {
        if (!d3.event.active) simulation.alphaTarget(0.3).restart();
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(d) {
        d.fx = d3.event.x;
        d.fy = d3.event.y;
    }

    function dragended(d) {
        if (!d3.event.active) simulation.alphaTarget(0);
        d.fx = null;
        d.fy = null;
    }
};