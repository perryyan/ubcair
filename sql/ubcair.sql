--
-- 	Database Table Creation
--
--		This file will create the tables for use with the book 
--  Database Management Systems by Raghu Ramakrishnan and Johannes Gehrke.
--  It is run automatically by the installation script.
--
--	Version 0.1.0.0 2002/04/05 by: David Warden.
--	Copyright (C) 2002 McGraw-Hill Companies Inc. All Rights Reserved.

-- Set the datetime datatype
-- format: 2014-09-01 15:05
ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI';
set pagesize 1000
set linesize 32767
set space 3

-- set the current date?
select sysdate from dual;
--
--  First drop any existing tables. Any errors are ignored.
--
drop table Customer cascade constraints;
drop table Airport cascade constraints;
-- Plane = Plane_in
drop table Plane cascade constraints;
drop table Flight cascade constraints;

drop table make_res cascade constraints;
drop table res_includes cascade constraints;

drop table has_B cascade constraints;
drop table last_location cascade constraints;

drop table payment cascade constraints;
drop table deter_pay cascade constraints;




--
-- Now, add each table.
--
-- Customer is not in BCNF
create table Customer(
	cid number(9,0) PRIMARY KEY,
	email varchar2(30) UNIQUE,
	password varchar(16),
	cname varchar2(20),
	passport_country varchar2(3),
	passport_num number(7,0),
	phone varchar2(20),
	address varchar2(150)
	);		
--alter table Customer
--add constraint invalid_passport_num
--check ((arrivalTime-departTime) <= interval '24' hour);
	
create table Airport(
	code varchar2(4) PRIMARY KEY,
	apname varchar2(50),
	city varchar2(15),
	country varchar2(4)
	);

create table Plane(
	pid varchar2(10) PRIMARY KEY,
	plane_model varchar2(20),
	airline varchar2(40),
	currAP varchar2(4) NOT NULL,
	FOREIGN KEY (currAP) references Airport(code)
	);
	
create table Flight(
	fid varchar2(10) PRIMARY KEY,
	departAP varchar2(4),
	arrivalAP varchar2(4),
	departTime TIMESTAMP,
	arrivalTime TIMESTAMP,
	pid varchar2(10) NOT NULL,
	cost decimal(6,2),
	FOREIGN KEY (pid) references Plane(pid),
	FOREIGN KEY (departAP) references Airport(code),
	FOREIGN KEY (arrivalAP) references Airport(code)
	);
column departTime format a9
column arrivalTime format a9

alter table Flight
add constraint Flight_time_check
check ((arrivalTime-departTime) <= interval '24' hour);
	
--now create tables for reservation	
-- class[economic = 0, business = 1, first = 2]
create table make_res(
	resid number(9,0) PRIMARY KEY,
	cid number(9,0) NOT NULL,
	pclass number(1,0),
	ticket_num number(1,0),
	FOREIGN KEY (cid) references Customer(cid)
	);
alter table make_res
add constraint ticket_num_not_0
check (ticket_num <> 0);
	
create table res_includes(
	fid varchar2(10) NOT NULL,
	resid number(9,0),
	resorder number(7,0),
	PRIMARY KEY (fid, resid),
	FOREIGN KEY (fid) references Flight(fid),
	FOREIGN KEY (resid) references make_res(resid)
	);
alter table res_includes
add constraint resorder_inbetween_1_and_3
check (resorder >= 1 AND resorder <= 3);
	
--create assertion fid_not_valid
--check (NOT exists ((select fid from res_includes) except (select fid from Flight)));
	
	
--now create tables for bags
-- status[in transit=0, lost=1, picked up=2, checked in=3]	
create table has_B(
	bid number(9,0) primary key,
	cid number(9,0) NOT NULL,
	status number(1,0),
	weight_kg decimal(6,2),
	--code varchar2(4),
	FOREIGN KEY (cid) references Customer(cid)
	);
alter table has_B
add constraint invalid_status
check (status >= 0 AND status <= 3);

create table last_location(
	bid number(9,0) PRIMARY KEY,
	code varchar2(4) NOT NULL,
	FOREIGN KEY (code) references Airport(code),
	FOREIGN KEY (bid) references has_B(bid)
	);
	

	
--now create tables for payment
-- so cid is not unique anymore
create table payment(
	payid number(9,0) PRIMARY KEY,
	creditcard number(12,0),
	cid number(9,0),
	FOREIGN KEY (cid) references Customer(cid)
	);
	
create table deter_pay(
	payid number(9,0),
	resid number(9,0) PRIMARY KEY,
	total_cost decimal(10,2),
	FOREIGN KEY (payid) references payment(payid),
	FOREIGN KEY (resid) references make_res(resid)
	);
CREATE ASSERTION assert check (
NOT exists ((select payid from payment) except (select payid from deter_pay)));	
	
--alter table deter_pay
--add constraint payid_check
--check (NOT exists ((select payid from payment) except (select payid from deter_pay)));
	


--
-- done adding all of the tables, now add in some tuples
--  first, add in the Customers, Airports, Planes, and Flights
-- Customers

-- Airports
insert into Airport VALUES ('YVR', 'Vancouver International Airport', 'Vancouver', 'CA');
insert into Airport VALUES ('LHR', 'London Heathrow Airport', 'London', 'UK');
insert into Airport VALUES ('HKG', 'Hong Kong International Airport', 'Hong Kong', 'HK');
insert into Airport VALUES ('SIN', 'Singapore Changi Airport', 'Singapore', 'SG');
insert into Airport VALUES ('ICN', 'Incheon International Airport', 'Incheon', 'KR');
insert into Airport VALUES ('TAK', 'Takamatsu Airport', 'Takamatsu', 'JP');
insert into Airport VALUES ('TPE', 'Taoyuan International Airport', 'Taipei', 'TW');
insert into Airport VALUES ('TXL', 'Tegel Airport', 'Berlin', 'DE');
insert into Airport VALUES ('PEK', 'Captical International Airport', 'Beijing', 'CN');
insert into Airport VALUES ('MXP', 'Malpensa Airport', 'Milano', 'IT');

-- Planes
insert into Plane VALUES ('AC005', 'Boeing747', 'Air Canada', 'YVR');
insert into Plane VALUES ('AC490', 'Boeing777', 'Air Canada', 'YVR');
insert into Plane VALUES ('BA007', 'Boeing747', 'British Airway', 'LHR');
insert into Plane VALUES ('BA156', 'Boeing777', 'British Airway', 'ICN');
insert into Plane VALUES ('CJ742', 'Boeing747', 'CityFlyer Express', 'LHR');
insert into Plane VALUES ('CP030', 'Boeing777', 'Cathay Pacific', 'SIN');
insert into Plane VALUES ('CP329', 'Boeing777', 'Cathay Pacific', 'HKG');
insert into Plane VALUES ('CP198', 'Boeing777', 'Cathay Pacific', 'TAK');
insert into Plane VALUES ('CP008', 'Boeing777', 'Cathay Pacific', 'TAK');
insert into Plane VALUES ('CZ222', 'Boeing747', 'China Southern Airlines', 'YVR');
insert into Plane VALUES ('CZ103', 'Boeing747', 'China Southern Airlines', 'HKG');
insert into Plane VALUES ('AZ021', 'Boeing777', 'Alitalia Express', 'MXP');
insert into Plane VALUES ('SA021', 'Boeing747', 'Singapore Airline', 'HKG');
insert into Plane VALUES ('KA074', 'Boeing747', 'Korean Air', 'ICN');
insert into Plane VALUES ('KA249', 'Boeing777', 'Korean Air', 'SIN');
insert into Plane VALUES ('AA403', 'Boeing747', 'Atlantis Airline', 'TAK');
insert into Plane VALUES ('AA221', 'Boeing747', 'Atlantis Airline', 'YVR');

-- Flights
insert into Flight VALUES ('10000', 'YVR', 'HKG', '2014-09-01 15:05', '2014-09-02 03:05', 'CP030', '750.00');
insert into Flight VALUES ('10030', 'YVR', 'HKG', '2014-09-01 14:15', '2014-09-02 02:05', 'AA221', '800.00');
insert into Flight VALUES ('10001', 'YVR', 'LHR', '2014-09-02 12:10', '2014-09-02 22:00', 'AC490', '350.00');
insert into Flight VALUES ('10002', 'YVR', 'SIN', '2014-09-02 02:00', '2014-09-02 16:20', 'AC005', '500.00');
insert into Flight VALUES ('10003', 'YVR', 'ICN', '2014-09-02 19:30', '2014-09-03 06:15', 'KA074', '550.00');
insert into Flight VALUES ('10004', 'YVR', 'PEK', '2014-09-01 07:15', '2014-09-01 17:55', 'CZ222', '500.00');
insert into Flight VALUES ('10005', 'LHR', 'SIN', '2014-09-01 18:30', '2014-09-02 02:00', 'BA156', '550.00');
insert into Flight VALUES ('10006', 'LHR', 'HKG', '2014-09-11 02:00', '2014-09-11 13:40', 'CP030', '500.00');
insert into Flight VALUES ('10007', 'LHR', 'MXP', '2014-09-02 03:10', '2014-09-02 06:10', 'AZ021', '250.00');
insert into Flight VALUES ('10008', 'LHR', 'TXL', '2014-09-02 18:10', '2014-09-02 20:00', 'BA007', '90.00');
insert into Flight VALUES ('10009', 'HKG', 'TAK', '2014-09-02 05:00', '2014-09-02 09:15', 'CP329', '200.00');
insert into Flight VALUES ('10010', 'HKG', 'TPE', '2014-09-07 21:00', '2014-09-08 02:15', 'CZ222', '80.00');
insert into Flight VALUES ('10011', 'HKG', 'PEK', '2014-09-02 04:00', '2014-09-02 06:50', 'CZ103', '65.00');
insert into Flight VALUES ('10012', 'HKG', 'YVR', '2014-09-12 13:20', '2014-09-13 02:10', 'CP329', '750.00');
insert into Flight VALUES ('10013', 'SIN', 'LHR', '2014-09-02 08:55', '2014-09-02 18:20', 'SA021', '350.00');
insert into Flight VALUES ('10014', 'ICN', 'YVR', '2014-09-02 18:00', '2014-09-03 07:50', 'KA074', '550.00');
insert into Flight VALUES ('10015', 'ICN', 'PEK', '2014-09-02 21:20', '2014-09-02 23:00', 'KA249', '120.00');
insert into Flight VALUES ('10016', 'TAK', 'YVR', '2014-09-12 09:10', '2014-09-12 22:00', 'AA403', '900.00');
insert into Flight VALUES ('10017', 'TAK', 'LHR', '2014-09-05 19:30', '2014-09-06 06:20', 'CP008', '1000.00');
insert into Flight VALUES ('10018', 'TAK', 'HKG', '2014-09-11 12:40', '2014-09-11 17:40', 'CP198', '100.00');
insert into Flight VALUES ('10019', 'TXL', 'MXP', '2014-09-02 22:50', '2014-09-03 00:00', 'BA007', '85.00');
insert into Flight VALUES ('10020', 'MXP', 'TXL', '2014-09-07 07:15', '2014-09-07 09:00', 'CJ742', '85.00');
insert into Flight VALUES ('10021', 'PEK', 'SIN', '2014-09-09 17:20', '2014-09-10 00:45', 'SA021', '170.00');
insert into Flight VALUES ('10022', 'PEK', 'HKG', '2014-09-07 15:45', '2014-09-07 17:30', 'CZ222', '65.00');
insert into Flight VALUES ('10023', 'PEK', 'TPE', '2014-09-02 08:40', '2014-09-02 11:35', 'CZ222', '200.00');

-- View sample
drop view Flight_time cascade constraints;
drop view trans1 cascade constraints;
drop view trans2 cascade constraints;
drop view trans3 cascade constraints;
drop view allFlight cascade constraints;


CREATE VIEW trans1(fid, dt1, depart, arrival, totalTime, totalprice) AS
	select f1.fid, f1.departTime, f1.departAP, f1.arrivalAP, (f1.arrivalTime-f1.departTime) AS totalTime, f1.cost
	from Flight f1;
column dt1 format a9
column totalTime format a9

CREATE VIEW trans2(firstid, secondid, dt1, depart, dt2, midd, arrival, totalTime, totalprice) AS
	select f1.fid, f2.fid, f1.departTime, f1.departAP, f2.departTime, f2.departAP, f2.arrivalAP, (f1.arrivalTime-f1.departTime)+(f2.arrivalTime-f2.departTime) AS totalTime, f1.cost+f2.cost AS totalprice
	from Flight f1, Flight f2
	where f1.arrivalAP = f2.departAP AND f2.departTime > f1.arrivalTime AND f1.departAP <> f2.arrivalAP AND (f2.arrivalTime-f1.departTime) <= '+000000001 23:59:59.000000000';
column dt1 format a9
column dt2 format a9
column totalTime format a9	

CREATE VIEW trans3(firstid, secondid, thirdid, dt1, depart, dt2, mid1, dt3, mid2, arrival, totalTime, totalprice) AS
	select f1.fid, f2.fid, f3.fid, f1.departTime, f1.departAP, f2.departTime, f2.departAP, f3.departTime, f3.departAP, f3.arrivalAP, (f1.arrivalTime-f1.departTime)+(f2.arrivalTime-f2.departTime)+(f3.arrivalTime-f3.departTime) AS totalTime, f1.cost+f2.cost+f3.cost AS totalprice
	from Flight f1, Flight f2, Flight f3
	where f1.arrivalAP = f2.departAP AND f2.arrivalAP = f3.departAP AND f2.departTime > f1.arrivalTime AND f3.departTime > f2.arrivalTime AND f1.departAP <> f2.arrivalAP AND f1.departAP<>f3.arrivalAP AND (f3.arrivalTime-f1.departTime) <= '+000000001 23:59:59.000000000';
column dt1 format a9
column dt2 format a9
column dt3 format a9
column totalTime format a9

CREATE VIEW allFlight(firstid, secondid, thirdid, dt1, depart, dt2, mid1, dt3, mid2, arrival, totalTime, totalprice) AS
	select t1.fid, NULL AS secondid, NULL AS thirdid, t1.dt1, t1.depart, NULL AS dt2, NULL AS mid1, NULL AS dt3, NULL AS mid2, t1.arrival, t1.totalTime, t1.totalprice
	from trans1 t1
UNION
	select t2.firstid, t2.secondid, NULL AS thirdid, t2.dt1, t2.depart, t2.dt2, t2.midd, NULL AS dt3, NULL AS mid2, t2.arrival, t2.totalTime, t2.totalprice 
	from trans2 t2
UNION
	select t3.firstid, t3.secondid, t3.thirdid, t3.dt1, t3.depart, t3.dt2, t3.mid1, t3.dt3, t3.mid2, t3.arrival, t3.totalTime, t3.totalprice 
	from trans3 t3;

CREATE VIEW Flight_time(fTime, depart, departCity, departCo, arrival, arrivalCity, arrivalCo) AS
	select f.totaltime, f.depart, a1.city, a1.country, f.arrival, a2.city, a2.country
	from allFlight f, airport a1, airport a2
	where f.depart = a1.code AND f.arrival = a2.code;
column fTime format a9	

-- query

-- other inserts samples
--Customer(cid, email, password, cname, passport_contry, passport_num, phone, address)
insert into Customer VALUES(0, 'shirley5001@hotmail.com', '1234', 'shirley', 'CN', '1234567', '7783212769', '1234567');
insert into Customer VALUES(1, '418446548@qq.com', '1234', 'shirley', 'CN', '1234567', '7783212769', '1234567');
--make_res(resid, cid, pclass, ticket_num)
insert into make_res VALUES(0, 0, 0, 1);
insert into make_res VALUES(1, 0, 0, 1);
insert into make_res VALUES(2, 1, 0, 2);
insert into make_res VALUES(3, 1, 1, 1);
--res_includes(fid, resid, resorder)
insert into res_includes VALUES(10000, 0, 1);
insert into res_includes VALUES(10000, 1, 1);
insert into res_includes VALUES(10004, 2, 1);
insert into res_includes VALUES(10023, 2, 2);
insert into res_includes VALUES(10030, 3, 1);

--has_B(bid, cid, status, weight_kg)
insert into has_B VALUES(0, 0, 3, 36.20);
insert into has_B VALUES(1, 0, 3, 28.00);
--last_location(bid, code)
insert into last_location VALUES(0, 'YVR');
insert into last_location VALUES(1, 'YVR');

-- more views
drop view Bag_num cascade constraints;
drop view Detail cascade constraints;
drop view Final_pay cascade constraints;

create VIEW Bag_num(cid, num_B) AS
	select h.cid, COUNT(bid)
	from has_B h
	group by h.cid;

create VIEW Detail(cid, resid, tcost) AS
	select cid, m.resid, (m.ticket_num*f.cost)+(m.pclass*100) AS tcost
	from Flight f, res_includes r, make_res m
	where m.resid = r.resid AND r.fid = f.fid;

	
-- do selection here
--payment(payid, creditcard, cid)
insert into payment VALUES(0, 23456789012, 0);
insert into payment VALUES(1, 123465749281, 1);

--deter_pay(payid, resid, total_cost)
insert into deter_pay
select p.payid, d.resid, SUM(d.tcost) AS total_cost
from payment p, detail d
where p.cid = d.cid
group by p.payid, d.resid;

--query on final cost
create view Final_pay(payid, price) AS
	select payid, SUM(total_cost)
	from deter_pay
	group by payid;

	
-- more queries
-- nested aggregation
-- find the company that has more then one plane and find times used of those plane and the average cost of their flights
--Plane(pid, plane_model, airline, currAP)
--Flight(fid, departAP, arrivalAP, arrivalTime, pid, cost)

select p2.pid, count(f.fid) AS f_num, AVG(f.cost)
from flight f, plane p2
where f.pid = p2.pid AND p2.airline IN (select p.airline
                     from Plane p
                     group by p.airline
                     having COUNT(p.pid)>1)
group by p2.pid;

-- division
-- find the reservation ID that used both fid 10030 and 10011, then 10004 and 10023
select r.resid
from res_includes r
where r.fid = '10030' AND r.resid = (select resid
                                     from res_includes r2
                                     where r2.fid = '10011');

select r.resid
from res_includes r
where r.fid = '10004' AND r.resid = (select resid
                                     from res_includes r2
                                     where r2.fid = '10023');
									 

								






