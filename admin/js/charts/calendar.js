/**
 *
 * @param {object} options
 * @param {string} options.selector
 * @param {object[]} options.data
 * @param {string} options.date
 * @param {string} options.color
 * @returns {null}
 */
const drawCalendar = function(options){
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
    const colorKey = options.color ? options.color : "color";


    const inputData = options.data.sort((a, b) => new Date(a[dateKey]) - new Date(b[dateKey]));

    const dateValues = inputData.map(dv => ({
        date: d3.timeDay(new Date(dv[dateKey])),
        value: Number(dv[colorKey])
    }));


    const svg = d3.select(node).append("svg");

    const { width, height } = node.getBoundingClientRect();
    svg.attr("width", width).attr("height", height);

    const years = d3
        .nest()
        .key(d => d.date.getUTCFullYear())
        .entries(dateValues)
        .reverse();

    const values = dateValues.map(c => c.value);
    const maxValue = d3.max(values);
    const minValue = d3.min(values);

    const cellSize = 15;
    const yearHeight = cellSize * 7;

    const group = svg.append("g");

    const year = group
        .selectAll("g")
        .data(years)
        .join("g")
        .attr(
            "transform",
            (d, i) => `translate(50, ${yearHeight * i + cellSize * 1.5})`
        );

    year
        .append("text")
        .attr("x", -5)
        .attr("y", -30)
        .attr("text-anchor", "end")
        .attr("font-size", 16)
        .attr("font-weight", 550)
        .attr("transform", "rotate(270)")
        .text(d => d.key);

    const formatDay = d =>
        ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"][d.getUTCDay()];
    const countDay = d => d.getUTCDay();
    const timeWeek = d3.utcSunday;
    const formatDate = d3.utcFormat("%x");
    const colorFn = d3
        .scaleSequential(d3.interpolateBuGn)
        .domain([Math.floor(minValue), Math.ceil(maxValue)]);
    const format = d3.format("+.2%");

    year
        .append("g")
        .attr("text-anchor", "end")
        .selectAll("text")
        .data(d3.range(7).map(i => new Date(1995, 0, i)))
        .join("text")
        .attr("x", -5)
        .attr("y", d => (countDay(d) + 0.5) * cellSize)
        .attr("dy", "0.31em")
        .attr("font-size", 12)
        .text(formatDay);

    year
        .append("g")
        .selectAll("rect")
        .data(d => d.values)
        .join("rect")
        .attr("width", cellSize - 1.5)
        .attr("height", cellSize - 1.5)
        .attr(
            "x",
            (d, i) => timeWeek.count(d3.utcYear(d.date), d.date) * cellSize + 10
        )
        .attr("y", d => countDay(d.date) * cellSize + 0.5)
        .attr("fill", d => colorFn(d.value))
        .append("title")
        .text(d => `${formatDate(d.date)}: ${d.value.toFixed(2)}`);

    const legend = group
        .append("g")
        .attr(
            "transform",
            `translate(10, ${years.length * yearHeight + cellSize * 4})`
        );

    const categoriesCount = 10;
    const categories = [...Array(categoriesCount)].map((_, i) => {
        const upperBound = (maxValue / categoriesCount) * (i + 1);
        const lowerBound = (maxValue / categoriesCount) * i;

        return {
            upperBound,
            lowerBound,
            color: d3.interpolateBuGn(upperBound / maxValue),
            selected: true
        };
    });

    const legendWidth = 60;

    function toggle(legend) {
        const { lowerBound, upperBound, selected } = legend;

        legend.selected = !selected;

        const highlightedDates = years.map(y => ({
            key: y.key,
            values: y.values.filter(
                v => v.value > lowerBound && v.value <= upperBound
            )
        }));

        year
            .data(highlightedDates)
            .selectAll("rect")
            .data(d => d.values, d => d.date)
            .transition()
            .duration(500)
            .attr("fill", d => (legend.selected ? colorFn(d.value) : "white"));
    }

    legend
        .selectAll("rect")
        .data(categories)
        .enter()
        .append("rect")
        .attr("fill", d => d.color)
        .attr("x", (d, i) => legendWidth * i)
        .attr("width", legendWidth)
        .attr("height", 15)
        .on("click", toggle);

    legend
        .selectAll("text")
        .data(categories)
        .join("text")
        .attr("transform", "rotate(90)")
        .attr("y", (d, i) => -legendWidth * i)
        .attr("dy", -30)
        .attr("x", 18)
        .attr("text-anchor", "start")
        .attr("font-size", 11)
        .text(d => `${d.lowerBound.toFixed(2)} - ${d.upperBound.toFixed(2)}`);

    legend
        .append("text")
        .attr("dy", -5)
        .attr("font-size", 14)
        .attr("text-decoration", "underline")
        .text("Click on category to select/deselect days");

    return svg.node();
};


function drawCalendar2(options) {
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
    const inputData = options.data.sort((a, b) => new Date(a[dateKey]) - new Date(b[dateKey]));

    var calendarRows = function(month) {
        var m = d3.timeMonth.floor(month);
        return d3.timeWeeks(d3.timeWeek.floor(m), d3.timeMonth.offset(m,1)).length;
    }

    var minDate = d3.min(inputData, function(d) { return new Date(d[dateKey]); });
    var maxDate = d3.max(inputData, function(d) { return new Date(d[dateKey]); });

    var cellMargin = 2,
        cellSize = 20;

    var day = d3.timeFormat("%w"),
        week = d3.timeFormat("%U"),
        format = d3.timeFormat("%Y-%m-%d"),
        titleFormat = d3.utcFormat("%a, %d-%b"),
        monthName = d3.timeFormat("%B"),
        months= d3.timeMonth.range(d3.timeMonth.floor(minDate), maxDate);

    for(var i=0; i<inputData.length; i++){
        inputData[i].today =  inputData[i][dateKey].slice(0,10);
    }

    var svg = d3.select(node).selectAll("svg")
        .data(months)
        .enter().append("svg")
        .attr("class", "month")
        .attr("width", (cellSize * 7) + (cellMargin * 8) )
        .attr("height", function(d) {
            var rows = calendarRows(d);
            return (cellSize * rows) + (cellMargin * (rows + 1)) + 20; // the 20 is for the month labels
        })
        .append("g")

    svg.append("text")
        .attr("class", "month-name")
        .attr("x", ((cellSize * 7) + (cellMargin * 8)) / 2 )
        .attr("y", 15)
        .attr("text-anchor", "middle")
        .text(function(d) { return monthName(d); })

    var rect = svg.selectAll("rect.day")
        .data(function(d, i) {
            return d3.timeDays(d, new Date(d.getFullYear(), d.getMonth()+1, 1));
        })
        .enter().append("rect")
        .attr("class", "day")
        .attr("width", cellSize)
        .attr("height", cellSize)
        .attr("rx", 3).attr("ry", 3) // rounded corners
        .attr("fill", '#eaeaea') // default light grey fill
        .attr("x", function(d) {
            return (day(d) * cellSize) + (day(d) * cellMargin) + cellMargin;
        })
        .attr("y", function(d) {
            return ((week(d) - week(new Date(d.getFullYear(),d.getMonth(),1))) * cellSize) +
                ((week(d) - week(new Date(d.getFullYear(),d.getMonth(),1))) * cellMargin) +
                cellMargin + 20;
        })
        .on("mouseover", function(d) {
            d3.select(this).classed('hover', true);
        })
        .on("mouseout", function(d) {
            d3.select(this).classed('hover', false);
        })
        .datum(format);

    rect.append("title")
        .text(function(d) { return titleFormat(new Date(d)); });

    var lookup = d3.nest()
        .key(function(d) { return d.today; })
        .rollup(function(leaves) { return leaves.length; })
        .object(inputData);

    count = d3.nest()
        .key(function(d) { return d.today; })
        .rollup(function(leaves) { return leaves.length; })
        .entries(inputData);

    scale = d3.scaleLinear()
        .domain(d3.extent(count, function(d) { return d.value; }))
        .range([0.4,1]); // the interpolate used for color expects a number in the range [0,1] but i don't want the lightest part of the color scheme

    rect.filter(function(d) { return d in lookup; })
        .style("fill", function(d) { return d3.interpolatePuBu(scale(lookup[d])); })
        .classed("clickable", true)
        .on("click", function(d){
            if(d3.select(this).classed('focus')){
                d3.select(this).classed('focus', false);
            } else {
                d3.select(this).classed('focus', true)
            }
            // doSomething();
        })
        .select("title")
        .text(function(d) { return titleFormat(new Date(d)) + ":  " + lookup[d]; });

}