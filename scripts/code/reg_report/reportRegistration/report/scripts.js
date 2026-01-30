let filterData = {}, currentFilters = {};

const filterMapping = {
    tags: "tag_id",
    razdel: "razdel_id",
    manager: "manager_id",
    partner: "partner_id",
    utm_source: "utm_source",
    utm_medium: "utm_medium",
    utm_campaign: "utm_campaign",
    utm_content: "utm_content",
    utm_term: "utm_term"
};

let first = true;

document.addEventListener('DOMContentLoaded', function() {


    const modal = document.querySelector('#detailsModal');
    const closeModalBtn = document.querySelector(".close-btn");
    // console.log(modal);

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Optional: Close the modal if user clicks outside of it
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    const tabs = document.querySelectorAll('.nav-link'); 
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault(); 

            if(first === true) document.getElementById('filtersForm').dispatchEvent(new Event('submit'));

            // Deactivate all tabs
            tabs.forEach(innerTab => {
                innerTab.classList.remove('active');
            });

            // Deactivate all tab contents
            const tabContents = document.querySelectorAll('.tab-pane');
            tabContents.forEach(content => {
                content.classList.remove('show');
                content.classList.remove('active');
            });

            // Activate the clicked tab
            event.target.classList.add('active');

            // Special handling for UTM tab
            if (event.target.id === 'byUtm-tab') {
                // Activate all UTM report contents
                document.getElementById('reportByUtmTable').classList.add('show');
                document.getElementById('reportByUtmTable').classList.add('active');
                const utmContents = document.querySelectorAll('.utm-report-content');
                utmContents.forEach(content => {
                    content.classList.add('show');
                    content.classList.add('active');
                });
            } else {
                // Activate the corresponding tab content
                const targetContent = document.getElementById(event.target.getAttribute('href').substr(1)); 
                targetContent.classList.add('show');
                targetContent.classList.add('active');
            }
        });
    });


    fetch('/server.php')
    .then(response => response.json()) 
    // .then(response => response.text())
    // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
    .then(data => {
        filterData = data;

        document.querySelector("input[name='startDate']").value = data.earliest_date;
        document.querySelector("input[name='endDate']").value = data.current_date;

        // console.log(data);
        
        const prependEmptyOption = (selectElement) => {
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '-- Выберите --'; 
            // console.log(selectElement)
            selectElement.prepend(emptyOption);
        };


        const razdelSelect = document.querySelector('[name="razdel"]');
        prependEmptyOption(razdelSelect);
        data.razdel.forEach(razdel => {
            const option = document.createElement('option');
            option.value = razdel.id;
            option.textContent = razdel.name;
            razdelSelect.appendChild(option);
        });

        const tagsSelect = document.querySelector('[name="tags"]');
        prependEmptyOption(tagsSelect);
        data.tags.forEach(tag => {
            const option = document.createElement('option');
            option.value = tag.id;
            option.textContent = tag.name;
            tagsSelect.appendChild(option);
        });

        const managerSelect = document.querySelector('[name="manager"]');
        prependEmptyOption(managerSelect);
        data.manager.forEach(manager => {
            const option = document.createElement('option');
            option.value = manager.id; 
            option.textContent = manager.name; 
            managerSelect.appendChild(option);
        });

        const partnerSelect = document.querySelector('[name="partner"]');
        prependEmptyOption(partnerSelect);
        data.partner.forEach(partner => {
            const option = document.createElement('option');
            option.value = partner.id; 
            option.textContent = partner.name; 
            partnerSelect.appendChild(option);
        });

        const utmSourceSelect = document.querySelector('[name="utm_source"]');
        prependEmptyOption(utmSourceSelect);
        data.utm.utm_source.forEach(source => {
            const option = document.createElement('option');
            option.value = source;
            option.textContent = source;
            utmSourceSelect.appendChild(option);
        });

        const utmMediumSelect = document.querySelector('[name="utm_medium"]');
        prependEmptyOption(utmMediumSelect);
        data.utm.utm_medium.forEach(medium => {
            const option = document.createElement('option');
            option.value = medium;
            option.textContent = medium;
            utmMediumSelect.appendChild(option);
        });

        const utmCampaignSelect = document.querySelector('[name="utm_campaign"]');
        prependEmptyOption(utmCampaignSelect);
        data.utm.utm_campaign.forEach(campaign => {
            const option = document.createElement('option');
            option.value = campaign;
            option.textContent = campaign;
            utmCampaignSelect.appendChild(option);
        });

        const utmContentSelect = document.querySelector('[name="utm_content"]');
        prependEmptyOption(utmContentSelect);
        data.utm.utm_content.forEach(content => {
            const option = document.createElement('option');
            option.value = content;
            option.textContent = content;
            utmContentSelect.appendChild(option);
        });

        const utmTermSelect = document.querySelector('[name="utm_term"]');
        prependEmptyOption(utmTermSelect);
        data.utm.utm_term.forEach(term => {
            const option = document.createElement('option');
            option.value = term;
            option.textContent = term;
            utmTermSelect.appendChild(option);
        });


    }).catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
});

const formDataToJSON = formData => {
    const obj = {};
    formData.forEach((value, key) => {
        if (obj[key]) {
            if (Array.isArray(obj[key])) {
                obj[key].push(value);
            } else {
                obj[key] = [obj[key], value];
            }
        } else {
            obj[key] = value;
        }
    });
    return obj;
};


document.getElementById('filtersForm').addEventListener('submit', function(e) {
    e.preventDefault();
    first = false;

    clearAllTables();


    

    const formData = new FormData(this);

    const rawFilters = formDataToJSON(formData);
    const mappedFilters = {}
    for (let key in rawFilters) {
        if (filterMapping[key]) {
            mappedFilters[filterMapping[key]] = rawFilters[key];
        }
    }
    currentFilters = [mappedFilters]


    document.getElementById("dataLoader").style.display = "block";
    // Fetch report data from server
    fetch('/server.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    // .then(response => response.text())
    // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
    .then(data => {
        
        const groupings = [
            {
                name: 'Day', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Week', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    const yearStart = new Date(Date.UTC(date.getFullYear(), 0, 1));
                    const weekNo = Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
                    return `W${weekNo}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Month', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `${date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Quarter', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `Q${Math.ceil((date.getMonth() + 1) / 3)}.${date.getFullYear()}`;
                }
            },
            { name: 'Razdel', keyFunc: item => item.razdel_name },
            { name: 'Tag', keyFunc: item => item.tag_name },
            { name: 'Manager', keyFunc: item => item.manager },
            { name: 'Partner', keyFunc: item => item.partner },
            { name: 'UtmSource', keyFunc: item => item.utm_source },
            { name: 'UtmMedium', keyFunc: item => item.utm_medium },
            { name: 'UtmCampaign', keyFunc: item => item.utm_campaign },
            { name: 'UtmContent', keyFunc: item => item.utm_content },
            { name: 'UtmTerm', keyFunc: item => item.utm_term }
        ];

        const flattenedData = [];
            Object.keys(data).forEach(key => {
                data[key].forEach(item => {
                    item.category = key;
                    flattenedData.push(item);
                });
            });

            const groupData = {};
            groupings.forEach(grouping => {
                groupData[grouping.name] = {};
            });

            function isValidDate(dateString) {
                const date = new Date(dateString);
                return !isNaN(date.getTime());
            }


            function getFilterFromItem(item, filterName) {
                if (item.category === 'dates') return { registrationDate: item.registrationDate };
                
                if(filterName === 'Razdel') return {razdel_id: item.razdel_id};
                if(filterName === 'Tag') return {tag_id: item.tag_id};
                if(filterName === 'Manager') return {manager_id: item.manager_id};
                if(filterName === 'Partner') return {partner_id: item.partner_id};
                if(filterName === 'UtmSource') return {utm_source: item.utm_source};
                if(filterName === 'UtmMedium') return {utm_medium: item.utm_medium};
                if(filterName === 'UtmCampaign') return {utm_campaign: item.utm_campaign};
                if(filterName === 'UtmContent') return {utm_content: item.utm_content};
                if(filterName === 'UtmTerm') return {utm_term: item.utm_term};
            }

            
            const dateBasedGroupings = ['Day', 'Week', 'Month', 'Quarter'];
            const seenUids = { Day: [], Week: [], Month: [], Quarter: [] };
            data["dates"].forEach(item => {
                if (item && item.uid) {
                    dateBasedGroupings.forEach(groupingName => {
                        const keyFunc = groupings.find(g => g.name === groupingName).keyFunc;
                        const key = keyFunc(item);

                        if (key && isValidDate(item.registrationDate)) {
                            const filter = getFilterFromItem(item, groupingName);
                            if (!groupData[groupingName][key]) {
                                groupData[groupingName][key] = {
                                    count: 0,
                                    ids: [],
                                    uids: [],
                                    filters: [filter],
                                    sum: 0
                                };
                            }
                            const currentGroup = groupData[groupingName][key];
                            if (currentGroup.ids.includes(item.id)) return;
                            currentGroup.sum++;
                            currentGroup.ids.push(item.id);
                            if (!currentGroup.filters.includes(filter)) currentGroup.filters.push(filter);
                            if (!currentGroup.uids.includes(item.uid)) {
                                currentGroup.uids.push(item.uid);
                                if (!seenUids[groupingName].includes(item.uid)) {
                                    currentGroup.count++;
                                    seenUids[groupingName].push(item.uid);
                                }
                            }
                        }
                    });
                }
            });


            // Process other non-date-based groupings
            flattenedData.forEach(item => {
                if (item && item.uid) {
                    groupings.forEach(grouping => {
                        if (!dateBasedGroupings.includes(grouping.name)) {
                            const key = grouping.keyFunc(item);
                            if (key){
                                const filter = getFilterFromItem(item, grouping.name);
                                // console.log(key, grouping.name, filter)
                                if (!groupData[grouping.name][key]) {
                                    groupData[grouping.name][key] = {
                                        count: 1,
                                        ids: [item.id],
                                        uids: [item.uid],
                                        filters: [filter],
                                        sum: 1
                                    };
                                } else {
                                    
                                    if(groupData[grouping.name][key].ids.includes(item.id) === true) return;
                                    groupData[grouping.name][key].sum ++;
                                    groupData[grouping.name][key].ids.push(item.id);
                                    if(groupData[grouping.name][key].filters.includes(filter) === false)groupData[grouping.name][key].filters.push(filter)
                                    if(groupData[grouping.name][key].uids.includes(item.uid) === true) return;
                                    groupData[grouping.name][key].uids.push(item.uid);
                                    groupData[grouping.name][key].count ++;
                                }
                            }
                        }
                    });
                }
            });


            groupings.forEach(grouping => {
                populateTable(`reportBy${grouping.name}Table`, groupData[grouping.name]);
            });

            

            const allPopulated = groupings.every(grouping => {
                const tableElement = document.getElementById(`reportBy${grouping.name}Table`);
                return tableElement && tableElement.getAttribute('data-populated') === 'true';
            });
            
            if (allPopulated) {
                document.getElementById("dataLoader").style.display = "none";
            }
        }).catch(error => {
            console.error("Error fetching details:", error);
            document.getElementById("dataLoader").style.display = "none";
        });;

        


        function populateTable(tableId, data) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            tableBody.innerHTML = '';
            
            let totalUniqueCount = 0;
            let totalAmount = 0;
            
            // Determine if the table should be sorted by date or sum
            const isDateBasedReport = ['reportByDayTable', 'reportByWeekTable', 'reportByMonthTable', 'reportByQuarterTable'].includes(tableId);
            
            let sortedData;
            if (isDateBasedReport) {
                sortedData = Object.entries(data);
                sortedData.reverse(); 
            } else {
                // Sort by sum in decrementing order for non-date based reports
                sortedData = Object.entries(data).sort((a, b) => b[1].sum - a[1].sum);
            }
            
            sortedData.forEach(([key, item]) => {
                totalUniqueCount += item.count;
                totalAmount += item.sum;
            
                const row = document.createElement('tr');

                row.addEventListener('click', (event) => {
                    showRegistrationDetails(item.uids, item.filters);
                });
            
                const cells = [
                    key,
                    parseInt(item.count),
                    '', // Unique Percentage (placeholder)
                    parseInt(item.sum),
                    ''  // Total Percentage (placeholder)
                ];
            
                cells.forEach(cellText => {
                    const cell = document.createElement('td');
                    cell.textContent = cellText;
                    row.appendChild(cell);
                });
            
                tableBody.appendChild(row);
            });
            
            document.querySelector(`#${tableId}-unique-count`).textContent = totalUniqueCount;
            document.querySelector(`#${tableId}-total-count`).textContent = totalAmount;
            
            // Fill in the percentage cells
            Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                const uniqueCount = parseInt(row.children[1].textContent);
                const totalCount = parseInt(row.children[3].textContent);
                row.children[2].textContent = ((uniqueCount / totalUniqueCount) * 100).toFixed(2) + '%';
                row.children[4].textContent = ((totalCount / totalAmount) * 100).toFixed(2) + '%';
            });

            const tableElement = document.getElementById(tableId);
            tableElement.setAttribute('data-populated', 'true');
        }


        function showRegistrationDetails(uids, filters = []) {
            
            const uidString = uids.join(',');
        
            // Build filters query string
            let filterQueries = [];
            currentFilters.forEach(filter => {
                for (let key in filterMapping) {
                    if (filter[filterMapping[key]] && !filterQueries.includes(`${filterMapping[key]}=${filter[filterMapping[key]]}`)) {
                        filterQueries.push(`${filterMapping[key]}=${filter[filterMapping[key]]}`);
                    }
                }
            });
            // console.log(filters)
            filters.forEach(filter => {

                if (filter.registrationDate && !filterQueries.includes(`registrationDate[]=${filter.registrationDate}`)) filterQueries.push(`registrationDate[]=${filter.registrationDate}`);
                if (filter.tag_id && !filterQueries.includes(`tag_id=${filter.tag_id}`)) filterQueries.push(`tag_id=${filter.tag_id}`);
                if (filter.razdel_id && !filterQueries.includes(`razdel_id=${filter.razdel_id}`)) filterQueries.push(`razdel_id=${filter.razdel_id}`);
                if (filter.manager_id && !filterQueries.includes(`manager_id=${filter.manager_id}`)) filterQueries.push(`manager_id=${filter.manager_id}`);
                if (filter.partner_id && !filterQueries.includes(`partner_id=${filter.partner_id}`)) filterQueries.push(`partner_id=${filter.partner_id}`);
                if (filter.utm_source && !filterQueries.includes(`utm_source=${filter.utm_source}`)) filterQueries.push(`utm_source=${filter.utm_source}`);
                if (filter.utm_medium && !filterQueries.includes(`utm_medium=${filter.utm_medium}`)) filterQueries.push(`utm_medium=${filter.utm_medium}`);
                if (filter.utm_campaign && !filterQueries.includes(`utm_campaign=${filter.utm_campaign}`)) filterQueries.push(`utm_campaign=${filter.utm_campaign}`);
                if (filter.utm_content && !filterQueries.includes(`utm_content=${filter.utm_content}`)) filterQueries.push(`utm_content=${filter.utm_content}`);
                if (filter.utm_term && !filterQueries.includes(`utm_term=${filter.utm_term}`)) filterQueries.push(`utm_term=${filter.utm_term}`);
            })
            // console.log(filterQueries)
            
            
            // Combine the UIDs and filter queries
            const queryString = `uids=${uidString}&${filterQueries.join('&')}`;
            // console.log(queryString)
        
            // Making AJAX request to server.php
            document.getElementById("dataLoader").style.display = "block";
            fetch(`server.php?action=getDetailsByUid&${queryString}`)
                .then(response => response.json())
                // .then(response => response.text())
                // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
                .then(data => {
                    // console.log(data.length)
                    if (data && data.length) {
                        const detailsModal = document.querySelector("#detailsModal");
                        // Clear previous table data
                        const existingTable = detailsModal.querySelector("table");
                        if(existingTable) {
                            existingTable.remove();
                        }
        
                        // Use the retrieved data
                        data.forEach(registration => {
                            // If table does not exist, create it
                            if (!detailsModal.querySelector("table")) {
                                const table = document.createElement('table');
                                const thead = document.createElement('thead');
                                const tbody = document.createElement('tbody');
                                
                                const headers = ['Дата Регистрации', 'UID', 'ФИО', 'Рекомендовал', 'Партнер'];
                                const headerRow = document.createElement('tr');
                                headers.forEach(headerText => {
                                    const th = document.createElement('th');
                                    th.textContent = headerText;
                                    headerRow.appendChild(th);
                                });
                                thead.appendChild(headerRow);
                                table.appendChild(thead);
                                table.appendChild(tbody);
                                
                                detailsModal.querySelector(".modal-content").appendChild(table);
                            }
                        
                            const tableBody = detailsModal.querySelector("tbody");
                        
                            const row = document.createElement('tr');
                        
                            const date = new Date(registration.registrationDate);

                            const day = date.getDate().toString().padStart(2, '0');
                            const month = (date.getMonth() + 1).toString().padStart(2, '0');
                            const year = date.getFullYear();

                            const formattedDate = `${day}.${month}.${year}`;

                            const dateCell = document.createElement('td');
                            dateCell.textContent = formattedDate;
                            row.appendChild(dateCell);
                        
                            const uidCell = document.createElement('td');
                            uidCell.textContent = registration.uid;
                            row.appendChild(uidCell);
                        
                            const nameSurnameCell = document.createElement('td');
                            nameSurnameCell.textContent = registration.name + ' ' + registration.surname; // Adjust based on your column names
                            row.appendChild(nameSurnameCell);
                        
                            const usernameCell = document.createElement('td');
                            usernameCell.textContent = registration.real_user_name; // Adjust based on your column names
                            row.appendChild(usernameCell);
                        
                            const userIdCell = document.createElement('td');
                            userIdCell.textContent = registration.username;
                            row.appendChild(userIdCell);
                        
                            tableBody.appendChild(row);
                        });
        
                        // Display the modal
                        detailsModal.style.display = 'block';

                    }
                    document.getElementById("dataLoader").style.display = "none";
                })
                .catch(error => {
                    console.error("Error fetching details:", error);
                    document.getElementById("dataLoader").style.display = "none";
                });
        }
        
        
        
        
        
    
        

    function clearAllTables() {
        const tables = document.querySelectorAll("#reportTabContent table");
        tables.forEach(table => {
            table.setAttribute('data-populated', 'false');
            const tbody = table.querySelector("tbody");
            if (tbody) {
                tbody.innerHTML = '';
            }
    
            // Clear the totals in the tfoot section
            const tfoot = table.querySelector("tfoot");
            if (tfoot) {
                const totalCountCell = tfoot.querySelector("[id$='-total-count']");
                
                if (totalCountCell) {
                    totalCountCell.textContent = '';
                }
    
            }
        });
    }
});




function clearFilters() {
    // Reset date inputs to default values
    document.querySelector("input[name='startDate']").value = filterData.earliest_date;
    document.querySelector("input[name='endDate']").value = filterData.current_date;
    
    // Clear select dropdowns
    const selectElements = [
        '[name="utm_source"]', '[name="utm_medium"]', '[name="utm_campaign"]',
        '[name="utm_content"]', '[name="utm_term"]', '[name="razdel"]',
        '[name="tags"]', '[name="manager"]', '[name="partner"]', '[name="product"]'
    ];
    
    selectElements.forEach(selector => {
        const select = document.querySelector(selector);
        if (select) {
            select.selectedIndex = 0;
        }
    });
}


document.getElementById('dropFiltersBtn').addEventListener('click', function() {
    clearFilters();
});


