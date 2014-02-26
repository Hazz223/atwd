/**
 * Description of Charts
 * Script for creating the charts
 *
 * @author hlp2-winser
 */

// https://developers.google.com/chart/

google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(CreateCharts);

// Creates both the bar chart and the pie chart based on data taken from the json
function CreateCharts(crimeData, pieDiv, barDiv, title, chartColumnOneName, chartColumnTwoName) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', chartColumnOneName);
    data.addColumn('number', chartColumnTwoName);
    
    // Cycles through the json data and adding it to the chart data var
    $.each(crimeData, function() {
        data.addRows([[this.id, this.total]]);
    });

    // Options for the Pie chart
    var options = {
        'title': title,
        backgroundColor: "transparent"};

    // Options for the Bar chart
    var barOptions = {
        title: title,
        bar: {groupWidth: "95%"},
        backgroundColor: "transparent"
    };
    
    // The creation of the charts
    var pieChart = new google.visualization.PieChart(pieDiv);
    var barChart = new google.visualization.ColumnChart(barDiv);
    pieChart.draw(data, options);
    barChart.draw(data, barOptions);
}