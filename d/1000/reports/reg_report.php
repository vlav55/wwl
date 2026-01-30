<?
include "/var/www/vlav/data/www/wwl/inc/top.class.php";
chdir("..");
include "init.inc.php";
$css="<LINK REL='StyleSheet' HREF='https://for16.ru/css/reg_report_styles.css' TYPE='text/css'>";
$t=new top($database,'Отчеты по регистрациям', true);
$db=new db($database);
chdir("reports");
?>
    <div class="container mt-5">
        <h1 class="mb-4">Отчеты по Регистрациям</h1>

        <!-- Filters -->
        <form id="filtersForm" class="mb-4">
            <div class="form-row">
                <!-- Date Range -->
                <div class="col-md-3">
                    <label>С:</label>
                    <input type="date" class="form-control" name="startDate">
                </div>
                <div class="col-md-3">
                    <label>Дата окончания:</label>
                    <input type="date" class="form-control" name="endDate">
                </div>
                
                <!-- Razdel Filters -->
                <div class="col-md-3">
                    <label>Этап:</label>
                    <select class="form-control" name="razdel">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
                
            </div>

            <div class="form-row mt-3">
                <!-- Tag Filters -->
                <div class="col-md-4">
                    <label>Теги:</label>
                    <select class="form-control" name="tags">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>

                <!-- Manager Filters -->
                <div class="col-md-4">
                    <label>Менеджеры:</label>
                    <select class="form-control" name="manager">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>

                <!-- Partner Filters -->
                <div class="col-md-4">
                    <label>Партнеры:</label>
                    <select class="form-control" name="partner">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>

            </div>

            <div class="row mt-4"> <!-- Added a margin-top for separation -->
                <div class="col-md-12">
                    <h4>UTM Filters</h4>
                </div>
                
                <!-- UTM Source Filter -->
                <div class="col-md-2">
                    <label>UTM Source:</label>
                    <select class="form-control" name="utm_source">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
            
                <!-- UTM Medium Filter -->
                <div class="col-md-2">
                    <label>UTM Medium:</label>
                    <select class="form-control" name="utm_medium">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
            
                <!-- UTM Campaign Filter -->
                <div class="col-md-2">
                    <label>UTM Campaign:</label>
                    <select class="form-control" name="utm_campaign">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
            
                <!-- UTM Content Filter -->
                <div class="col-md-2">
                    <label>UTM Content:</label>
                    <select class="form-control" name="utm_content">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
            
                <!-- UTM Term Filter -->
                <div class="col-md-2">
                    <label>UTM Term:</label>
                    <select class="form-control" name="utm_term">
                        <!-- Options will be filled dynamically -->
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Создать отчет</button>
            
        </form>
        <button id="dropFiltersBtn" class="btn btn-secondary mt-4">Сбросить фильтры</button>

        <!-- Tab navigation -->
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active report-nav-link" id="byDay-tab" data-toggle="tab" href="#reportByDayTable" role="tab">Регистрации по дням</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byWeek-tab" data-toggle="tab" href="#reportByWeekTable" role="tab">Регистрации по неделям</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byMonth-tab" data-toggle="tab" href="#reportByMonthTable" role="tab">Регистрации по месяцам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byQuarter-tab" data-toggle="tab" href="#reportByQuarterTable" role="tab">Регистрации по кварталам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byRazdel-tab" data-toggle="tab" href="#reportByRazdelTable" role="tab">Регистрации по этапам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byTag-tab" data-toggle="tab" href="#reportByTagTable" role="tab">Регистрации по тэгам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byManager-tab" data-toggle="tab" href="#reportByManagerTable" role="tab">Регистрации по менеджерам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byPartner-tab" data-toggle="tab" href="#reportByPartnerTable" role="tab">Регистрации по партнерам</a>
            </li>
            <li class="nav-item">
                <a class="nav-link report-nav-link" id="byUtm-tab" data-toggle="tab" href="#reportByUtmTable" role="tab">Регистрации по utm</a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="loader" id="dataLoader" style="display: none"></div>

        <div class="tab-content" id="reportTabContent"></div>

        <script>
            const reportTypes = [
                { title: "Регистрации по дням за период", id: "reportByDayTable", colName: "Дата" },
                { title: "Регистрации по неделям за период", id: "reportByWeekTable", colName: "Неделя" },
                { title: "Регистрации по месяцам за период", id: "reportByMonthTable", colName: "Месяц" },
                { title: "Регистрации по кварталам за период", id: "reportByQuarterTable", colName: "Квартал" },
                { title: "Регистрации по этапам за период", id: "reportByRazdelTable", colName: "Этап" },
                { title: "Регистрации по тэгам за период", id: "reportByTagTable", colName: "Тэг" },
                { title: "Регистрации по менеджерам за период", id: "reportByManagerTable", colName: "Менеджер" },
                { title: "Регистрации по партнерам за период", id: "reportByPartnerTable", colName: "Партнер" },
            ];

            const utmReportTypes = [
                { title: "Регистрации по utm_source за период", id: "reportByUtmSourceTable", colName: "UTM Source" },
                { title: "Регистрации по utm_medium за период", id: "reportByUtmMediumTable", colName: "UTM Medium" },
                { title: "Регистрации по utm_campaign за период", id: "reportByUtmCampaignTable", colName: "UTM Campaign" },
                { title: "Регистрации по utm_content за период", id: "reportByUtmContentTable", colName: "UTM Content" },
                { title: "Регистрации по utm_term за период", id: "reportByUtmTermTable", colName: "UTM Term" }
            ];


            
            const generateReportSection = ({ title, id, colName }, index, isActive = false, className = '') => `
                <div class="tab-pane fade ${className} ${isActive ? 'show active' : ''}" id="${id}">
                    <h2 class="mt-5">${title}</h2>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th scope="col">${colName}</th>
                                <th scope="col">Количество уникальных регистраций</th>
                                <th scope="col">Процент от общего по уникальным</th>
                                <th scope="col">Количество всего</th>
                                <th scope="col">Процент</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be filled dynamically here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Итого:</th>
                                <th id="${id}-unique-count"></th>
                                <th id="${id}-unique-percentage">100%</th>
                                <th id="${id}-total-count"></th>
                                <th id="${id}-total-percentage">100%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            const generateUtmReportLinks = () => `
                <div class="tab-pane fade" id="reportByUtmTable">
                    <h2 class="mt-5">Оплаты по utm</h2>
                    <ul>
                        ${utmReportTypes.map(type => `<li><a href="#${type.id}">${type.title}</a></li>`).join('')}
                    </ul>
                </div>
            `;

            const reportContent = reportTypes.map((type, index) => generateReportSection(type, index, index === 0)).join('');
            const utmReportContent = generateUtmReportLinks() + utmReportTypes.map((type, index) => generateReportSection(type, index, false, 'utm-report-content')).join('');

            document.getElementById('reportTabContent').innerHTML = reportContent + utmReportContent;

        </script>



        <script src="reg_report_scripts.js"></script>
    </div>

    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Детали Регистрации</h2>
            <!-- The registration details will be added here by the JavaScript -->
        </div>
    </div>

<?$t->bottom();?>
