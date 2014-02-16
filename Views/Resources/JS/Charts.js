google.load('visualization', '1.0', {'packages': ['corechart']});
google.setOnLoadCallback(CreateCharts);

function CreateCharts(crimeData, pieDiv, barDiv, title) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('number', 'Total');

    $.each(crimeData, function() {
        data.addRows([[this.id, this.total]]);
    });

    var options = {
        'title': title,
        backgroundColor: "transparent"};

    var barOptions = {
        title: title,
        bar: {groupWidth: "95%"},
        backgroundColor: "transparent"
    };

    var pieChart = new google.visualization.PieChart(pieDiv);
    var barChart = new google.visualization.ColumnChart(barDiv);
    pieChart.draw(data, options);
    barChart.draw(data, barOptions);
}